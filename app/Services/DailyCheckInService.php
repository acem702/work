<?php

namespace App\Services;

use App\Models\User;
use App\Models\DailyCheckIn;
use Carbon\Carbon;
use DB;
use Exception;

class DailyCheckInService
{
    protected $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    /**
     * Check if user can check in today
     */
    public function canCheckIn(User $user)
    {
        $today = Carbon::today();
        
        // Check if already checked in today
        $alreadyCheckedIn = DailyCheckIn::where('user_id', $user->id)
            ->where('check_in_date', $today)
            ->exists();

        return !$alreadyCheckedIn;
    }

    /**
     * Get user's streak status
     */
    public function getStreakStatus(User $user)
    {
        $today = Carbon::today();
        $lastCheckIn = $user->last_check_in_date ? Carbon::parse($user->last_check_in_date) : null;

        // Calculate if streak should reset
        if ($lastCheckIn) {
            $daysDifference = $lastCheckIn->diffInDays($today);
            
            if ($daysDifference > 1) {
                // Streak broken - reset
                $currentStreak = 0;
            } else {
                $currentStreak = $user->current_streak;
            }
        } else {
            $currentStreak = 0;
        }

        // Get next reward day (1-7, cycles)
        $nextDay = ($currentStreak % 7) + 1;

        // Get check-in history for the week
        $weekCheckIns = DailyCheckIn::where('user_id', $user->id)
            ->where('check_in_date', '>=', $today->copy()->subDays(6))
            ->orderBy('check_in_date')
            ->get();

        return [
            'current_streak' => $currentStreak,
            'next_day' => $nextDay,
            'next_reward' => DailyCheckIn::getRewardForDay($nextDay),
            'can_check_in' => $this->canCheckIn($user),
            'week_check_ins' => $weekCheckIns,
            'total_check_ins' => $user->total_check_ins,
        ];
    }

    /**
     * Process check-in
     */
    public function checkIn(User $user)
    {
        if (!$this->canCheckIn($user)) {
            throw new Exception('You have already checked in today!');
        }

        return DB::transaction(function () use ($user) {
            $today = Carbon::today();
            $lastCheckIn = $user->last_check_in_date ? Carbon::parse($user->last_check_in_date) : null;

            // Calculate current streak
            if ($lastCheckIn && $lastCheckIn->diffInDays($today) === 1) {
                // Consecutive day
                $currentStreak = $user->current_streak + 1;
            } else {
                // First check-in or streak broken
                $currentStreak = 1;
            }

            // Calculate streak day (1-7, cycles)
            $streakDay = (($currentStreak - 1) % 7) + 1;

            // Get reward for this day
            $rewardAmount = DailyCheckIn::getRewardForDay($streakDay);

            // Add reward to user balance
            $balanceBefore = $user->point_balance;
            $user->point_balance += $rewardAmount;
            $user->current_streak = $currentStreak;
            $user->last_check_in_date = $today;
            $user->total_check_ins += 1;
            $user->save();

            // Create check-in record
            $checkIn = DailyCheckIn::create([
                'user_id' => $user->id,
                'check_in_date' => $today,
                'streak_day' => $streakDay,
                'reward_amount' => $rewardAmount,
            ]);

            // Record transaction
            $this->transactionService->recordTransaction(
                user: $user,
                type: 'daily_check_in',
                amount: $rewardAmount,
                balanceBefore: $balanceBefore,
                balanceAfter: $user->point_balance,
                description: "Daily check-in reward (Day {$streakDay})",
                relatedTaskId: null
            );

            return [
                'check_in' => $checkIn,
                'streak_day' => $streakDay,
                'reward_amount' => $rewardAmount,
                'current_streak' => $currentStreak,
                'new_balance' => $user->point_balance,
            ];
        });
    }

    /**
     * Get check-in calendar for the current week
     */
    public function getWeekCalendar(User $user)
    {
        $today = Carbon::today();
        $startOfWeek = $today->copy()->startOfWeek();
        
        $calendar = [];
        $checkIns = DailyCheckIn::where('user_id', $user->id)
            ->where('check_in_date', '>=', $startOfWeek)
            ->where('check_in_date', '<=', $today)
            ->get()
            ->keyBy('check_in_date');

        for ($i = 0; $i < 7; $i++) {
            $date = $startOfWeek->copy()->addDays($i);
            $dateStr = $date->toDateString();
            
            $calendar[] = [
                'date' => $date,
                'day_name' => $date->format('D'),
                'is_today' => $date->isToday(),
                'is_past' => $date->isPast() && !$date->isToday(),
                'is_future' => $date->isFuture(),
                'checked_in' => isset($checkIns[$dateStr]),
                'reward' => isset($checkIns[$dateStr]) ? $checkIns[$dateStr]->reward_amount : DailyCheckIn::getRewardForDay($i + 1),
                'streak_day' => isset($checkIns[$dateStr]) ? $checkIns[$dateStr]->streak_day : $i + 1,
            ];
        }

        return $calendar;
    }
}