<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\User;

class TransactionService
{
    /**
     * Record a transaction
     */
    public function recordTransaction(
        User $user,
        string $type,
        float $amount,
        float $balanceBefore,
        float $balanceAfter,
        string $description,
        ?int $relatedTaskId = null,
        ?int $processedBy = null
    ) {
        return Transaction::create([
            'user_id' => $user->id,
            'type' => $type,
            'amount' => $amount,
            'balance_before' => $balanceBefore,
            'balance_after' => $balanceAfter,
            'description' => $description,
            'related_task_id' => $relatedTaskId,
            'processed_by' => $processedBy,
        ]);
    }

    /**
     * Admin top-up user points
     */
    public function topUpPoints(User $user, float $amount, string $reason, User $admin)
    {
        $balanceBefore = $user->point_balance;
        $user->point_balance += $amount;
        $user->save();

        return $this->recordTransaction(
            user: $user,
            type: 'admin_topup',
            amount: $amount,
            balanceBefore: $balanceBefore,
            balanceAfter: $user->point_balance,
            description: "Admin top-up: {$reason}",
            processedBy: $admin->id
        );
    }
}