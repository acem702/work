<?php

namespace App\Http\Controllers;

use App\Services\DailyCheckInService;
use Illuminate\Http\Request;

class DailyCheckInController extends Controller
{
    protected $checkInService;

    public function __construct(DailyCheckInService $checkInService)
    {
        $this->checkInService = $checkInService;
    }

    public function claim(Request $request)
    {
        try {
            $user = auth()->user();

            // CRITICAL: Check if user has completed required tasks
            if ($user->tasks_completed_today < $user->membershipTier->daily_task_limit) {
                return back()->with('error', 'You must complete all daily tasks before claiming check-in reward!');
            }

            // Check if can check in
            if (!$this->checkInService->canCheckIn($user)) {
                return back()->with('error', 'You have already checked in today!');
            }

            // Process check-in
            $result = $this->checkInService->checkIn($user);

            return back()->with('success', "Check-in successful! You earned $" . number_format($result['reward_amount'], 2) . " (Day {$result['streak_day']})");

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}