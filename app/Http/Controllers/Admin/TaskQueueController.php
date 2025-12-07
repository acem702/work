<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\TaskQueueService;
use App\Models\User;
use App\Models\Product;
use App\Models\ComboTask;
use App\Models\MembershipTier;
use Illuminate\Http\Request;
use Exception;

class TaskQueueController extends Controller
{
    protected $taskQueueService;

    public function __construct(TaskQueueService $taskQueueService)
    {
        $this->middleware('admin');
        $this->taskQueueService = $taskQueueService;
    }

    /**
     * Display task assignment page
     */
    public function index()
    {
        $users = User::with('membershipTier')->where('role', '!=', 'admin')->get();
        $products = Product::active()->with('minMembershipTier')->get();
        $comboTasks = ComboTask::active()->with('items.product')->get();
        $membershipTiers = MembershipTier::active()->ordered()->get();

        return view('admin.task-queue.index', compact('users', 'products', 'comboTasks', 'membershipTiers'));
    }

    /**
     * Assign tasks to specific user
     */
    public function assignToUser(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'product_ids' => 'required_without:combo_task_ids|array',
            'product_ids.*' => 'exists:products,id',
            'combo_task_ids' => 'required_without:product_ids|array',
            'combo_task_ids.*' => 'exists:combo_tasks,id',
        ]);

        try {
            $user = User::findOrFail($request->user_id);
            $results = [];

            // Assign regular products
            if (!empty($request->product_ids)) {
                $productQueues = $this->taskQueueService->assignProductsToUser($user, $request->product_ids);
                $results['products'] = $productQueues;
            }

            // Assign combo tasks
            if (!empty($request->combo_task_ids)) {
                $comboQueues = [];
                foreach ($request->combo_task_ids as $comboTaskId) {
                    $comboQueues[] = $this->taskQueueService->assignComboTaskToUser($user, $comboTaskId);
                }
                $results['combos'] = $comboQueues;
            }

            $totalCount = count($results['products'] ?? []) + count($results['combos'] ?? []);

            return response()->json([
                'success' => true,
                'message' => $totalCount . ' tasks assigned to ' . $user->name,
                'results' => $results,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Assign tasks to multiple users
     */
    public function assignToMultipleUsers(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'exists:users,id',
            'product_ids' => 'required_without:combo_task_ids|array',
            'product_ids.*' => 'exists:products,id',
            'combo_task_ids' => 'required_without:product_ids|array',
            'combo_task_ids.*' => 'exists:combo_tasks,id',
        ]);

        try {
            $results = [];

            foreach ($request->user_ids as $userId) {
                $user = User::findOrFail($userId);
                $userResults = [];

                // Assign regular products
                if (!empty($request->product_ids)) {
                    $userResults['products'] = $this->taskQueueService->assignProductsToUser($user, $request->product_ids);
                }

                // Assign combo tasks
                if (!empty($request->combo_task_ids)) {
                    $comboQueues = [];
                    foreach ($request->combo_task_ids as $comboTaskId) {
                        $comboQueues[] = $this->taskQueueService->assignComboTaskToUser($user, $comboTaskId);
                    }
                    $userResults['combos'] = $comboQueues;
                }

                $results[$userId] = $userResults;
            }

            return response()->json([
                'success' => true,
                'message' => 'Tasks assigned to ' . count($request->user_ids) . ' users',
                'results' => $results,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Assign tasks to membership tier
     */
    public function assignToTier(Request $request)
    {
        $request->validate([
            'tier_level' => 'required|integer|exists:membership_tiers,level',
            'product_ids' => 'required_without:combo_task_ids|array',
            'product_ids.*' => 'exists:products,id',
            'combo_task_ids' => 'required_without:product_ids|array',
            'combo_task_ids.*' => 'exists:combo_tasks,id',
        ]);

        try {
            $results = [];

            // Assign regular products
            if (!empty($request->product_ids)) {
                $productResults = $this->taskQueueService->assignProductsToMembershipTier(
                    $request->tier_level,
                    $request->product_ids
                );
                $results['products'] = $productResults;
            }

            // Assign combo tasks
            if (!empty($request->combo_task_ids)) {
                foreach ($request->combo_task_ids as $comboTaskId) {
                    $comboResults = $this->taskQueueService->assignComboTaskToMembershipTier(
                        $request->tier_level,
                        $comboTaskId
                    );
                    $results['combo_' . $comboTaskId] = $comboResults;
                }
            }

            $successCount = 0;
            foreach ($results as $resultSet) {
                if (is_array($resultSet)) {
                    $successCount += collect($resultSet)->filter(fn($r) => !isset($r['error']))->count();
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Tasks assigned to {$successCount} users in the tier",
                'results' => $results,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * View user's task queue
     */
    public function userQueue($userId)
    {
        $user = User::with([
            'taskQueues.product',
            'taskQueues.comboTask.items.product',
            'taskQueues.product.minMembershipTier',
            'taskQueues.comboTask.items.product.minMembershipTier',
            'membershipTier',
            'tasks'
        ])->findOrFail($userId);
        
        return view('admin.task-queue.user-queue', compact('user'));
    }

    /**
     * Remove task from queue
     */
    public function destroy($taskQueueId)
    {
        $taskQueue = \App\Models\TaskQueue::findOrFail($taskQueueId);
        
        if ($taskQueue->status !== 'queued') {
            return response()->json([
                'success' => false,
                'message' => 'Can only remove queued tasks'
            ], 400);
        }
        
        $taskQueue->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Task removed from queue'
        ]);
    }
}