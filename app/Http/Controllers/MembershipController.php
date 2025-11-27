<?php

namespace App\Http\Controllers;

use App\Models\MembershipTier;
use Illuminate\Http\Request;

class MembershipController extends Controller
{
    /**
     * Display membership page
     */
    public function index()
    {
        return view('membership.index');
    }

    /**
     * Get all membership tiers (API endpoint)
     */
    public function tiers()
    {
        $tiers = MembershipTier::active()
            ->ordered()
            ->get()
            ->map(function ($tier) {
                return [
                    'id' => $tier->id,
                    'name' => $tier->name,
                    'level' => $tier->level,
                    'upgrade_cost' => number_format($tier->upgrade_cost, 2),
                    'benefits' => $this->getTierBenefits($tier),
                ];
            });

        return response()->json([
            'success' => true,
            'tiers' => $tiers,
        ]);
    }

    /**
     * Generate benefits list for a tier
     */
    private function getTierBenefits($tier)
    {
        return [
            "Receive a set of {$tier->daily_task_limit} data optimization tasks.",
            "Profit for each data optimization is " . ($tier->commission_multiplier * 100 - 100) . "%.",
            "Merged data optimization profit is " . ($tier->commission_multiplier * 100) . "%.",
            "Activate with {$tier->upgrade_cost} USD.",
            "Up to {$tier->daily_task_limit} sets of data optimization tasks can be completed per day.",
        ];
    }
}