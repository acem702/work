<?php

namespace App\Services;

use App\Models\User;
use App\Models\MembershipTier;
use DB;
use Exception;

class MembershipService
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
     * Upgrade user membership
     */
    public function upgradeMembership(User $user, MembershipTier $newTier)
    {
        return DB::transaction(function () use ($user, $newTier) {
            $currentTier = $user->membershipTier;

            // Validate upgrade
            if ($newTier->level <= $currentTier->level) {
                throw new Exception('Can only upgrade to higher membership tiers.');
            }

            // Check balance
            if (!$user->hasSufficientBalance($newTier->upgrade_cost)) {
                throw new Exception('Insufficient points for membership upgrade.');
            }

            // Deduct upgrade cost
            $balanceBefore = $user->point_balance;
            $user->point_balance -= $newTier->upgrade_cost;
            $user->membership_tier_id = $newTier->id;
            $user->save();

            // Record transaction
            $this->transactionService->recordTransaction(
                user: $user,
                type: 'membership_upgrade',
                amount: -$newTier->upgrade_cost,
                balanceBefore: $balanceBefore,
                balanceAfter: $user->point_balance,
                description: "Upgraded to {$newTier->name} membership"
            );

            // Process referral earnings
            $this->referralService->processMembershipUpgradeEarnings($user, $newTier->upgrade_cost);

            return $user->fresh();
        });
    }

    /**
     * Get available upgrades for user
     */
    public function getAvailableUpgrades(User $user)
    {
        return MembershipTier::where('level', '>', $user->membershipTier->level)
            ->active()
            ->ordered()
            ->get();
    }
}