<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\TaskQueueService;
use App\Models\User;
use App\Models\Product;
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
        $membershipTiers = MembershipTier::active()->ordered()->get();

        return view('admin.task-queue.index', compact('users', 'products', 'membershipTiers'));
    }

    /**
     * Assign tasks to specific user
     */
    public function assignToUser(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'product_ids' => 'required|array|min:1',
            'product_ids.*' => 'exists:products,id',
        ]);

        try {
            $user = User::findOrFail($request->user_id);
            $taskQueues = $this->taskQueueService->assignProductsToUser($user, $request->product_ids);

            return response()->json([
                'success' => true,
                'message' => count($taskQueues) . ' tasks assigned to ' . $user->name,
                'task_queues' => $taskQueues,
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
            'product_ids' => 'required|array|min:1',
            'product_ids.*' => 'exists:products,id',
        ]);

        try {
            $results = $this->taskQueueService->assignProductsToUsers(
                $request->user_ids,
                $request->product_ids
            );

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
            'product_ids' => 'required|array|min:1',
            'product_ids.*' => 'exists:products,id',
        ]);

        try {
            $results = $this->taskQueueService->assignProductsToMembershipTier(
                $request->tier_level,
                $request->product_ids
            );

            $successCount = collect($results)->filter(fn($r) => !isset($r['error']))->count();

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
        $user = User::with(['taskQueues.product', 'membershipTier'])->findOrFail($userId);
        
        return view('admin.task-queue.user-queue', compact('user'));
    }

    /**
     * Remove task from queue
     */
    public function destroy(TaskQueue $taskQueue)
    {
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