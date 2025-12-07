<?php

namespace App\Http\Controllers;

use App\Services\TaskService;
use Illuminate\Http\Request;
use Exception;

class TaskController extends Controller
{
    protected $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    /**
     * Display user's task dashboard
     */
    public function index()
    {
        $user = auth()->user();
        
        $pendingTask = $user->tasks()->pending()->with('product', 'comboTask')->first();
        $taskQueue = $user->taskQueues()
            ->queued()
            ->ordered()
            ->with(['product', 'comboTask.items.product'])
            ->get();
        $completedTasks = $user->tasks()->completed()->with('product')->latest()->paginate(10);
        
        $stats = [
            'total_completed' => $user->tasks()->completed()->count(),
            'total_earned' => $user->tasks()->completed()->sum('commission_earned'),
            'today_completed' => $user->tasks_completed_today,
            'daily_limit' => $user->membershipTier->daily_task_limit,
        ];

        return view('tasks.index', compact('pendingTask', 'taskQueue', 'completedTasks', 'stats'));
    }

    /**
     * Get next task
     */
    public function getNext()
    {
        try {
            $user = auth()->user();
            $taskQueue = $this->taskService->getNextTask($user);

            // Load appropriate relations based on task type
            if ($taskQueue->is_combo) {
                $taskQueue->load('comboTask.items.product.minMembershipTier');
            } else {
                $taskQueue->load('product.minMembershipTier');
            }

            return response()->json([
                'success' => true,
                'task_queue' => $taskQueue,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Start a task
     */
    public function start(Request $request)
    {
        $request->validate([
            'task_queue_id' => 'required|exists:task_queues,id',
        ]);

        try {
            $user = auth()->user();
            $taskQueue = $user->taskQueues()->findOrFail($request->task_queue_id);
            
            $task = $this->taskService->startTask($user, $taskQueue);

            // Load product relation
            $task->load('product', 'comboTask');

            return response()->json([
                'success' => true,
                'message' => 'Task started successfully. Points locked.',
                'task' => $task,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Submit/Complete a task
     */
    public function submit(Request $request)
    {
        $request->validate([
            'task_id' => 'required|exists:tasks,id',
        ]);

        try {
            $user = auth()->user();
            $task = $user->tasks()->findOrFail($request->task_id);
            
            // Check if task can be submitted
            if (!$this->taskService->canSubmitTask($task)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient balance to submit task. Please contact admin for top-up. Current balance: ' . number_format($user->point_balance, 2),
                ], 400);
            }
            
            $completedTask = $this->taskService->completeTask($task);

            // Check if this was a combo task and if there's a next task created
            $nextComboTask = null;
            if ($completedTask->combo_task_id && $completedTask->next_combo_task_id) {
                $nextComboTask = \App\Models\Task::find($completedTask->next_combo_task_id);
            }

            $response = [
                'success' => true,
                'message' => 'Task completed successfully!',
                'task' => $completedTask,
                'new_balance' => $user->fresh()->point_balance,
            ];

            // If there's a next combo task and user is in deficit, add a warning
            if ($nextComboTask && $user->fresh()->point_balance < 0) {
                $response['has_next_combo_task'] = true;
                $response['next_task_pending'] = true;
                $response['message'] = 'Task completed! Next combo task auto-started but insufficient balance. Please contact admin for top-up.';
            } elseif ($nextComboTask) {
                $response['has_next_combo_task'] = true;
                $response['next_task_pending'] = false;
                $response['message'] = 'Task completed! Next combo task is ready to submit.';
            }

            return response()->json($response);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Cancel a pending task
     */
    public function cancel(Request $request)
    {
        $request->validate([
            'task_id' => 'required|exists:tasks,id',
        ]);

        try {
            $user = auth()->user();
            $task = $user->tasks()->findOrFail($request->task_id);
            
            $cancelledTask = $this->taskService->cancelTask($task);

            return response()->json([
                'success' => true,
                'message' => 'Task cancelled. Points refunded.',
                'new_balance' => $user->fresh()->point_balance,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get task statistics
     */
    public function stats()
    {
        $user = auth()->user();
        
        $todaysEarnings = $user->tasks()
            ->whereDate('completed_at', today())
            ->sum('commission_earned');
        
        $frozenAmount = $user->tasks()
            ->where('status', 'pending')
            ->sum('points_locked');
        
        $balanceDue = $user->point_balance;
        
        return response()->json([
            'todaysEarnings' => number_format($todaysEarnings, 2),
            'frozenAmount' => number_format($frozenAmount, 2),
            'balanceDue' => number_format($balanceDue, 2),
        ]);
    }

    /**
     * Display orders/history page
     */
    public function orders()
    {
        return view('tasks.orders');
    }

    /**
     * Get task history (API endpoint)
     */
    public function history()
    {
        $user = auth()->user();
        
        $tasks = $user->tasks()
            ->with(['product.minMembershipTier', 'comboTask'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($task) use ($user) {
                // Handle combo tasks vs regular tasks
                if ($task->combo_task_id) {
                    $product = $task->product;
                    $comboTask = $task->comboTask;
                    
                    return [
                        'id' => $task->id,
                        'product_name' => $product->name . " (Combo: {$comboTask->name} - Step {$task->combo_sequence})",
                        'product_image' => $product->image_url ?? null,
                        'total_amount' => number_format($product->base_points, 2),
                        'commission' => number_format($task->commission_earned ?? 0, 2),
                        'task_progress' => $user->tasks_completed_today . '/' . $user->membershipTier->daily_task_limit,
                        'status' => $task->status,
                        'status_label' => ucfirst($task->status),
                        'created_at' => $task->created_at->format('Y-m-d H:i:s'),
                        'can_submit' => $task->status === 'pending' && $this->taskService->canSubmitTask($task),
                        'is_combo' => true,
                        'combo_sequence' => $task->combo_sequence,
                    ];
                } else {
                    $product = $task->product;
                    $commission = $task->status === 'completed' 
                        ? $task->commission_earned 
                        : $product->calculateCommission($user);
                    
                    return [
                        'id' => $task->id,
                        'product_name' => $product->name,
                        'product_image' => $product->image_url ?? null,
                        'total_amount' => number_format($product->base_points, 2),
                        'commission' => number_format($commission, 2),
                        'task_progress' => $user->tasks_completed_today . '/' . $user->membershipTier->daily_task_limit,
                        'status' => $task->status,
                        'status_label' => ucfirst($task->status),
                        'created_at' => $task->created_at->format('Y-m-d H:i:s'),
                        'can_submit' => $task->status === 'pending' && $this->taskService->canSubmitTask($task),
                        'is_combo' => false,
                    ];
                }
            });
        
        return response()->json([
            'success' => true,
            'tasks' => $tasks,
        ]);
    }
}