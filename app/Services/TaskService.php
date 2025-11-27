<?php

namespace App\Services;

use App\Models\Task;
use App\Models\TaskQueue;
use App\Models\User;
use App\Models\Product;
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
            ->with('product.minMembershipTier')
            ->first();

        if (!$taskQueue) {
            throw new Exception('No tasks available in your queue.');
        }

        // Verify product access
        if (!$taskQueue->product->isAccessibleBy($user)) {
            throw new Exception('Your membership level is insufficient for this task.');
        }

        // Allow task to be viewed regardless of balance
        return $taskQueue;
    }

    /**
     * Start a task (lock balance and create task in pending state)
     */
    public function startTask(User $user, TaskQueue $taskQueue)
    {
        return DB::transaction(function () use ($user, $taskQueue) {
            $product = $taskQueue->product;
            $pointsNeeded = $product->base_points;

            // Store balance before
            $balanceBefore = $user->point_balance;
            
            // Check if user has sufficient balance
            $hasSufficientBalance = $user->point_balance >= $pointsNeeded;
            
            if ($hasSufficientBalance) {
                // Normal flow: User has enough balance - lock the points
                $user->point_balance -= $pointsNeeded;
                $user->save();
                
                $description = "Points locked for task: {$product->name}";
            } else {
                // Insufficient balance: Lock ALL current balance, create negative for deficit
                $currentBalance = $user->point_balance;
                $deficit = $pointsNeeded - $currentBalance;
                
                // Lock the current balance (set to negative deficit)
                $user->point_balance = -$deficit;
                $user->save();
                
                $description = "Task started with insufficient balance. Locked: {$currentBalance}, Deficit: {$deficit}. Admin top-up required to submit.";
            }

            // Create task record in PENDING state
            $task = Task::create([
                'user_id' => $user->id,
                'product_id' => $product->id,
                'task_queue_id' => $taskQueue->id,
                'status' => 'pending', // Always starts as pending
                'points_locked' => $pointsNeeded,
                'balance_before' => $balanceBefore,
                'submitted_at' => now(),
            ]);

            // Record transaction for locked points
            $this->transactionService->recordTransaction(
                user: $user,
                type: 'task_lock',
                amount: -$pointsNeeded,
                balanceBefore: $balanceBefore,
                balanceAfter: $user->point_balance,
                description: $description,
                relatedTaskId: $task->id
            );

            // Activate task queue item
            $taskQueue->activate();

            // Return task with flag indicating if user can submit
            $task->can_submit = $hasSufficientBalance;
            return $task;
        });
    }

    /**
     * Check if user can submit the pending task
     */
    public function canSubmitTask(Task $task)
    {
        $user = $task->user;
    
        // User can submit ONLY if their current balance is >= 0
        // This means admin has topped them up sufficiently to cover the deficit
        return $user->point_balance >= 0;
    }

    /**
     * Complete a task (refund + commission) - only if balance is sufficient
     */
    public function completeTask(Task $task)
    {
        return DB::transaction(function () use ($task) {
            if ($task->status !== 'pending') {
                throw new Exception('Task is not in pending status.');
            }

            $user = $task->user;
            $product = $task->product;

            // Check if user can submit (has been topped up sufficiently)
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

            // Record refund transaction
            $this->transactionService->recordTransaction(
                user: $user,
                type: 'task_refund',
                amount: $task->points_locked,
                balanceBefore: $balanceBefore,
                balanceAfter: $balanceAfterRefund,
                description: "Points refunded for task: {$product->name}",
                relatedTaskId: $task->id
            );

            // Record commission transaction
            $this->transactionService->recordTransaction(
                user: $user,
                type: 'task_commission',
                amount: $commission,
                balanceBefore: $balanceAfterRefund,
                balanceAfter: $finalBalance,
                description: "Commission earned from task: {$product->name}",
                relatedTaskId: $task->id
            );

            // Complete task queue item
            $task->taskQueue?->complete();

            // Update product stats
            $product->increment('total_submissions');

            // Process referral earnings
            $this->referralService->processTaskReferralEarnings($task, $commission);

            return $task->fresh();
        });
    }

    /**
     * Cancel a pending task
     */
    public function cancelTask(Task $task)
    {
        return DB::transaction(function () use ($task) {
            if ($task->status !== 'pending') {
                throw new Exception('Only pending tasks can be cancelled.');
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