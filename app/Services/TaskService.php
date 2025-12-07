<?php

namespace App\Services;

use App\Models\Task;
use App\Models\TaskQueue;
use App\Models\User;
use App\Models\Product;
use App\Models\ComboTask;
use DB;
use Exception;

class TaskService
{
    protected $transactionService;
    protected $referralService;

    public function __construct(
        TransactionService $transactionService,
        ReferralService $referralService
    ) {
        $this->transactionService = $transactionService;
        $this->referralService = $referralService;
    }

    /**
     * Get next available task for user
     */
    public function getNextTask(User $user)
    {
        // Check if user has pending task
        if ($user->hasActivePendingTask()) {
            throw new Exception('You have a pending task. Please complete it first.');
        }

        // Check daily task limit
        if (!$user->canPerformTask()) {
            throw new Exception('Daily task limit reached. Try again tomorrow.');
        }

        // Get next queued task
        $taskQueue = $user->taskQueues()
            ->queued()
            ->ordered()
            ->with(['product.minMembershipTier', 'comboTask.items.product'])
            ->first();

        if (!$taskQueue) {
            throw new Exception('No tasks available in your queue.');
        }

        // Verify access based on task type
        if ($taskQueue->is_combo) {
            // For combo tasks, check if user can access the combo
            if (!$taskQueue->comboTask->isAccessibleBy($user)) {
                throw new Exception('Your membership level is insufficient for this combo task.');
            }
        } else {
            // For regular tasks, check product access
            if (!$taskQueue->product->isAccessibleBy($user)) {
                throw new Exception('Your membership level is insufficient for this task.');
            }
        }

        // Allow task to be viewed regardless of balance
        return $taskQueue;
    }

    /**
     * Start a task (handles both regular and combo tasks)
     */
    public function startTask(User $user, TaskQueue $taskQueue)
    {
        if ($taskQueue->is_combo) {
            return $this->startComboTask($user, $taskQueue);
        }

        return $this->startRegularTask($user, $taskQueue);
    }

    /**
     * Start a regular single task
     */
    protected function startRegularTask(User $user, TaskQueue $taskQueue)
    {
        return DB::transaction(function () use ($user, $taskQueue) {
            $product = $taskQueue->product;
            $pointsNeeded = $product->base_points;

            $balanceBefore = $user->point_balance;
            $hasSufficientBalance = $user->point_balance >= $pointsNeeded;
            
            if ($hasSufficientBalance) {
                $user->point_balance -= $pointsNeeded;
                $user->save();
                $description = "Points locked for task: {$product->name}";
            } else {
                $currentBalance = $user->point_balance;
                $deficit = $pointsNeeded - $currentBalance;
                $user->point_balance = -$deficit;
                $user->save();
                $description = "Task started with insufficient balance. Locked: {$currentBalance}, Deficit: {$deficit}. Admin top-up required to submit.";
            }

            $task = Task::create([
                'user_id' => $user->id,
                'product_id' => $product->id,
                'task_queue_id' => $taskQueue->id,
                'status' => 'pending',
                'points_locked' => $pointsNeeded,
                'balance_before' => $balanceBefore,
                'submitted_at' => now(),
            ]);

            $this->transactionService->recordTransaction(
                user: $user,
                type: 'task_lock',
                amount: -$pointsNeeded,
                balanceBefore: $balanceBefore,
                balanceAfter: $user->point_balance,
                description: $description,
                relatedTaskId: $task->id
            );

            $taskQueue->activate();

            $task->can_submit = $hasSufficientBalance;
            return $task;
        });
    }

    /**
     * Start a combo task - only creates the FIRST task in pending
     */
    protected function startComboTask(User $user, TaskQueue $taskQueue)
    {
        return DB::transaction(function () use ($user, $taskQueue) {
            $comboTask = ComboTask::with('items.product')->findOrFail($taskQueue->combo_task_id);
            
            // Get the first item in the combo
            $firstItem = $comboTask->items->first();
            $firstProduct = $firstItem->product;
            $pointsNeeded = $firstProduct->base_points;

            $balanceBefore = $user->point_balance;
            $hasSufficientBalance = $user->point_balance >= $pointsNeeded;
            
            if ($hasSufficientBalance) {
                $user->point_balance -= $pointsNeeded;
                $user->save();
                $description = "Points locked for combo task (1/{$comboTask->sequence_count}): {$firstProduct->name}";
            } else {
                $currentBalance = $user->point_balance;
                $deficit = $pointsNeeded - $currentBalance;
                $user->point_balance = -$deficit;
                $user->save();
                $description = "Combo task started with insufficient balance (1/{$comboTask->sequence_count}). Locked: {$currentBalance}, Deficit: {$deficit}. Admin top-up required.";
            }

            // Create ONLY the first task
            $task = Task::create([
                'user_id' => $user->id,
                'product_id' => $firstProduct->id,
                'task_queue_id' => $taskQueue->id,
                'combo_task_id' => $comboTask->id,
                'combo_sequence' => 1,
                'status' => 'pending',
                'points_locked' => $pointsNeeded,
                'balance_before' => $balanceBefore,
                'submitted_at' => now(),
            ]);

            $this->transactionService->recordTransaction(
                user: $user,
                type: 'task_lock',
                amount: -$pointsNeeded,
                balanceBefore: $balanceBefore,
                balanceAfter: $user->point_balance,
                description: $description,
                relatedTaskId: $task->id
            );

            $taskQueue->activate();

            $task->can_submit = $hasSufficientBalance;
            $task->combo_info = [
                'total_tasks' => $comboTask->sequence_count,
                'current_sequence' => 1,
                'combo_name' => $comboTask->name,
            ];
            
            return $task;
        });
    }

    /**
     * Check if user can submit the pending task
     */
    public function canSubmitTask(Task $task)
    {
        $user = $task->user->fresh(); // Always get fresh user data
        
        // CRITICAL: User can submit ONLY if their current balance is >= 0
        // This means admin has topped them up sufficiently to cover the deficit
        return $task->status === 'pending' && $user->point_balance >= 0;
    }

    /**
     * Complete a task (handles combo progression)
     */
    public function completeTask(Task $task)
    {
        return DB::transaction(function () use ($task) {
            if ($task->status !== 'pending') {
                throw new Exception('Task is not in pending status.');
            }

            $user = $task->user->fresh(); // Get fresh user data
            $product = $task->product;

            // CRITICAL CHECK: User MUST have balance >= 0 to submit
            if ($user->point_balance < 0) {
                throw new Exception('Cannot submit task with negative balance. Current balance: ' . number_format($user->point_balance, 2) . '. Please contact admin for top-up.');
            }

            // Double check using service method
            if (!$this->canSubmitTask($task)) {
                throw new Exception('Insufficient balance to submit task. Please contact admin for top-up.');
            }

            // Calculate commission
            $commission = $product->calculateCommission($user);

            // Refund locked points + add commission
            $balanceBefore = $user->point_balance;
            $user->point_balance += $task->points_locked + $commission;
            
            // Update daily task counter
            $user->tasks_completed_today += 1;
            $user->last_task_date = now();
            $user->save();

            $balanceAfterRefund = $balanceBefore + $task->points_locked;
            $finalBalance = $user->point_balance;

            // Update task
            $task->update([
                'status' => 'completed',
                'commission_earned' => $commission,
                'balance_after' => $finalBalance,
                'completed_at' => now(),
            ]);

            // Record transactions
            $this->transactionService->recordTransaction(
                user: $user,
                type: $task->combo_task_id ? 'task_refund' : 'task_refund',
                amount: $task->points_locked,
                balanceBefore: $balanceBefore,
                balanceAfter: $balanceAfterRefund,
                description: "Points refunded for task: {$product->name}",
                relatedTaskId: $task->id
            );

            $this->transactionService->recordTransaction(
                user: $user,
                type: $task->combo_task_id ? 'task_commission' : 'task_commission',
                amount: $commission,
                balanceBefore: $balanceAfterRefund,
                balanceAfter: $finalBalance,
                description: "Commission earned from task: {$product->name}",
                relatedTaskId: $task->id
            );

            // Update product stats
            $product->increment('total_submissions');

            // Process referral earnings
            $this->referralService->processTaskReferralEarnings($task, $commission);

            // **COMBO MAGIC: Check if this is part of a combo and trigger next task**
            if ($task->combo_task_id) {
                $nextTask = $this->triggerNextComboTask($task);
                if ($nextTask) {
                    $task->next_combo_task_id = $nextTask->id;
                    $task->save();
                }
            } else {
                // Regular task - complete the queue
                $task->taskQueue?->complete();
            }

            return $task->fresh();
        });
    }

    /**
     * Trigger the next task in a combo sequence
     * This automatically creates the next pending task and puts user in deficit
     */
    protected function triggerNextComboTask(Task $completedTask)
    {
        $user = $completedTask->user;
        $comboTask = ComboTask::with('items.product')->findOrFail($completedTask->combo_task_id);
        
        $nextSequence = $completedTask->combo_sequence + 1;
        
        // Check if there's a next task in the combo
        if ($nextSequence > $comboTask->sequence_count) {
            // Combo completed - mark task queue as complete
            $completedTask->taskQueue?->complete();
            return null;
        }

        // Get the next item
        $nextItem = $comboTask->items->where('sequence_order', $nextSequence)->first();
        if (!$nextItem) {
            return null;
        }

        $nextProduct = $nextItem->product;
        $pointsNeeded = $nextProduct->base_points;

        // **AUTO-LOCK POINTS FOR NEXT TASK** (will likely go negative)
        $balanceBefore = $user->fresh()->point_balance;
        $hasSufficientBalance = $balanceBefore >= $pointsNeeded;
        
        if ($hasSufficientBalance) {
            $user->point_balance -= $pointsNeeded;
            $user->save();
            $description = "Auto-locked points for combo task ({$nextSequence}/{$comboTask->sequence_count}): {$nextProduct->name}";
        } else {
            $currentBalance = $balanceBefore;
            $deficit = $pointsNeeded - $currentBalance;
            $user->point_balance = -$deficit;
            $user->save();
            $description = "Auto-locked for combo task ({$nextSequence}/{$comboTask->sequence_count}). Locked: {$currentBalance}, Deficit: {$deficit}. Admin top-up required.";
        }

        // Create the next pending task automatically
        $nextTask = Task::create([
            'user_id' => $user->id,
            'product_id' => $nextProduct->id,
            'task_queue_id' => $completedTask->task_queue_id,
            'combo_task_id' => $comboTask->id,
            'combo_sequence' => $nextSequence,
            'status' => 'pending',
            'points_locked' => $pointsNeeded,
            'balance_before' => $balanceBefore,
            'submitted_at' => now(),
        ]);

        $this->transactionService->recordTransaction(
            user: $user,
            type: 'task_lock',
            amount: -$pointsNeeded,
            balanceBefore: $balanceBefore,
            balanceAfter: $user->point_balance,
            description: $description,
            relatedTaskId: $nextTask->id
        );

        return $nextTask;
    }

    /**
     * Cancel a pending task (special handling for combo tasks)
     */
    public function cancelTask(Task $task)
    {
        return DB::transaction(function () use ($task) {
            if ($task->status !== 'pending') {
                throw new Exception('Only pending tasks can be cancelled.');
            }

            // For combo tasks, can only cancel if it's the first one OR if previous tasks are completed
            if ($task->combo_task_id && $task->combo_sequence > 1) {
                throw new Exception('Cannot cancel mid-combo task. Please complete or contact admin.');
            }

            $user = $task->user;
            
            // Refund locked points
            $balanceBefore = $user->point_balance;
            $user->point_balance += $task->points_locked;
            $user->save();

            // Update task status
            $task->update(['status' => 'cancelled']);

            // Record transaction
            $this->transactionService->recordTransaction(
                user: $user,
                type: 'task_refund',
                amount: $task->points_locked,
                balanceBefore: $balanceBefore,
                balanceAfter: $user->point_balance,
                description: "Points refunded - task cancelled: {$task->product->name}",
                relatedTaskId: $task->id
            );

            // Reset task queue to queued
            if ($task->taskQueue) {
                $task->taskQueue->update([
                    'status' => 'queued',
                    'activated_at' => null
                ]);
            }

            return $task;
        });
    }
}