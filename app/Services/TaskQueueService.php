<?php

namespace App\Services;

use App\Models\User;
use App\Models\Product;
use App\Models\TaskQueue;
use App\Models\ComboTask;
use DB;
use Exception;

class TaskQueueService
{
    /**
     * Assign regular products to user
     */
    public function assignProductsToUser(User $user, array $productIds)
    {
        return DB::transaction(function () use ($user, $productIds) {
            $maxOrder = TaskQueue::where('user_id', $user->id)->max('sequence_order') ?? 0;

            $taskQueues = [];
            foreach ($productIds as $index => $productId) {
                $product = Product::findOrFail($productId);

                if (!$product->isAccessibleBy($user)) {
                    throw new Exception("Product {$product->name} requires higher membership level.");
                }

                $taskQueues[] = TaskQueue::create([
                    'user_id' => $user->id,
                    'product_id' => $productId,
                    'sequence_order' => $maxOrder + $index + 1,
                    'status' => 'queued',
                    'is_combo' => false,
                ]);
            }

            return $taskQueues;
        });
    }

    /**
     * Assign combo task to user
     */
    public function assignComboTaskToUser(User $user, int $comboTaskId)
    {
        return DB::transaction(function () use ($user, $comboTaskId) {
            $comboTask = ComboTask::with('items.product')->findOrFail($comboTaskId);

            // Verify combo access
            if (!$comboTask->isAccessibleBy($user)) {
                throw new Exception("Combo task {$comboTask->name} requires higher membership level.");
            }

            $maxOrder = TaskQueue::where('user_id', $user->id)->max('sequence_order') ?? 0;

            // Create a SINGLE queue entry for the entire combo
            $taskQueue = TaskQueue::create([
                'user_id' => $user->id,
                'product_id' => null, // No single product
                'combo_task_id' => $comboTaskId,
                'is_combo' => true,
                'sequence_order' => $maxOrder + 1,
                'status' => 'queued',
            ]);

            return $taskQueue;
        });
    }

    /**
     * Assign combo tasks to multiple users
     */
    public function assignComboTaskToUsers(array $userIds, int $comboTaskId)
    {
        $results = [];

        foreach ($userIds as $userId) {
            $user = User::findOrFail($userId);
            try {
                $results[$userId] = $this->assignComboTaskToUser($user, $comboTaskId);
            } catch (Exception $e) {
                $results[$userId] = ['error' => $e->getMessage()];
            }
        }

        return $results;
    }

    /**
     * Assign combo task to membership tier
     */
    public function assignComboTaskToMembershipTier(int $tierLevel, int $comboTaskId)
    {
        $users = User::whereHas('membershipTier', function ($q) use ($tierLevel) {
            $q->where('level', $tierLevel);
        })->active()->get();

        $results = [];
        foreach ($users as $user) {
            try {
                $results[$user->id] = $this->assignComboTaskToUser($user, $comboTaskId);
            } catch (Exception $e) {
                $results[$user->id] = ['error' => $e->getMessage()];
            }
        }

        return $results;
    }

    /**
     * Assign products to multiple users
     */
    public function assignProductsToUsers(array $userIds, array $productIds)
    {
        $results = [];

        foreach ($userIds as $userId) {
            $user = User::findOrFail($userId);
            $results[$userId] = $this->assignProductsToUser($user, $productIds);
        }

        return $results;
    }

    /**
     * Assign products to all users of a membership tier
     */
    public function assignProductsToMembershipTier(int $tierLevel, array $productIds)
    {
        $users = User::whereHas('membershipTier', function ($q) use ($tierLevel) {
            $q->where('level', $tierLevel);
        })->active()->get();

        $results = [];
        foreach ($users as $user) {
            try {
                $results[$user->id] = $this->assignProductsToUser($user, $productIds);
            } catch (Exception $e) {
                $results[$user->id] = ['error' => $e->getMessage()];
            }
        }

        return $results;
    }

    /**
     * Remove task from queue
     */
    public function removeFromQueue(TaskQueue $taskQueue)
    {
        if ($taskQueue->status === 'active') {
            throw new Exception('Cannot remove active tasks from queue.');
        }

        if ($taskQueue->status === 'completed') {
            throw new Exception('Cannot remove completed tasks from queue.');
        }

        return $taskQueue->delete();
    }

    /**
     * Reorder user's task queue
     */
    public function reorderQueue(User $user, array $taskQueueIds)
    {
        return DB::transaction(function () use ($user, $taskQueueIds) {
            foreach ($taskQueueIds as $index => $taskQueueId) {
                TaskQueue::where('id', $taskQueueId)
                    ->where('user_id', $user->id)
                    ->where('status', 'queued')
                    ->update(['sequence_order' => $index + 1]);
            }

            return $user->taskQueues()->queued()->ordered()->get();
        });
    }
}