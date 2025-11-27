<?php

namespace App\Services;

use App\Models\Task;
use App\Models\User;
use App\Models\ReferralEarning;
use App\Models\ReferralSetting;
use DB;

class ReferralService
{
    protected $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    /**
     * Process referral earnings when user completes a task
     */
    public function processTaskReferralEarnings(Task $task, float $commission)
    {
        $user = $task->user;
        $currentReferrer = $user->referrer;
        $level = 1;

        while ($currentReferrer && $level <= 3) { // Support up to 3 levels
            $percentage = ReferralSetting::getPercentageForLevel($level);

            if ($percentage > 0) {
                $earning = ($commission * $percentage) / 100;

                // Add to referrer's balance
                DB::transaction(function () use ($currentReferrer, $earning, $user, $task, $level) {
                    $balanceBefore = $currentReferrer->point_balance;
                    $currentReferrer->point_balance += $earning;
                    $currentReferrer->save();

                    // Record referral earning
                    ReferralEarning::create([
                        'referrer_id' => $currentReferrer->id,
                        'referee_id' => $user->id,
                        'task_id' => $task->id,
                        'amount' => $earning,
                        'referral_level' => $level,
                        'earning_type' => 'task_commission',
                    ]);

                    // Record transaction
                    $this->transactionService->recordTransaction(
                        user: $currentReferrer,
                        type: 'referral_bonus',
                        amount: $earning,
                        balanceBefore: $balanceBefore,
                        balanceAfter: $currentReferrer->point_balance,
                        description: "Level {$level} referral bonus from {$user->name}'s task",
                        relatedTaskId: $task->id
                    );
                });
            }

            $currentReferrer = $currentReferrer->referrer;
            $level++;
        }
    }

    /**
     * Process referral earnings when user upgrades membership
     */
    public function processMembershipUpgradeEarnings(User $user, float $upgradeCost)
    {
        $currentReferrer = $user->referrer;
        $level = 1;

        while ($currentReferrer && $level <= 2) { // Support 2 levels for upgrades
            $percentage = ReferralSetting::getPercentageForLevel($level);

            if ($percentage > 0) {
                $earning = ($upgradeCost * $percentage) / 100;

                DB::transaction(function () use ($currentReferrer, $earning, $user, $level) {
                    $balanceBefore = $currentReferrer->point_balance;
                    $currentReferrer->point_balance += $earning;
                    $currentReferrer->save();

                    ReferralEarning::create([
                        'referrer_id' => $currentReferrer->id,
                        'referee_id' => $user->id,
                        'amount' => $earning,
                        'referral_level' => $level,
                        'earning_type' => 'membership_upgrade',
                    ]);

                    $this->transactionService->recordTransaction(
                        user: $currentReferrer,
                        type: 'referral_bonus',
                        amount: $earning,
                        balanceBefore: $balanceBefore,
                        balanceAfter: $currentReferrer->point_balance,
                        description: "Level {$level} referral bonus from {$user->name}'s membership upgrade"
                    );
                });
            }

            $currentReferrer = $currentReferrer->referrer;
            $level++;
        }
    }
}