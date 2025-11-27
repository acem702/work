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
        
        $pendingTask = $user->tasks()->pending()->with('product')->first();
        $taskQueue = $user->taskQueues()->queued()->ordered()->with('product')->get();
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

            return response()->json([
                'success' => true,
                'task_queue' => $taskQueue->load('product.minMembershipTier'),
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

            return response()->json([
                'success' => true,
                'message' => 'Task started successfully. Points locked.',
                'task' => $task->load('product'),
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
            if (!$task->canBeSubmitted()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient balance to submit task. Please contact admin for top-up. Current balance: ' . number_format($user->point_balance, 2),
                ], 400);
            }
            
            $completedTask = $this->taskService->completeTask($task);

            return response()->json([
                'success' => true,
                'message' => 'Task completed successfully!',
                'task' => $completedTask,
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
            ->with(['product.minMembershipTier'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($task) use ($user) {
                $product = $task->product;
                $commission = $task->status === 'completed' 
                    ? $task->commission_earned 
                    : $product->calculateCommission($user);
                
                // Calculate task progress (completed tasks / total limit)
                $completedToday = $user->tasks_completed_today;
                $dailyLimit = $user->membershipTier->daily_task_limit;
                
                return [
                    'id' => $task->id,
                    'product_name' => $product->name,
                    'product_image' => $product->image_url,
                    'total_amount' => number_format($product->base_points, 2),
                    'commission' => number_format($commission, 2),
                    'task_progress' => $completedToday . '/' . $dailyLimit,
                    'status' => $task->status,
                    'status_label' => ucfirst($task->status),
                    'created_at' => $task->created_at->format('Y-m-d H:i:s'),
                    'can_submit' => $task->status === 'pending' && $task->canBeSubmitted(),
                ];
            });
        
        return response()->json([
            'success' => true,
            'tasks' => $tasks,
        ]);
    }
}