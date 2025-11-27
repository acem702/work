<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReferralController extends Controller
{
    /**
     * Display referral page
     */
    public function index()
    {
        return view('referrals.index');
    }

    /**
     * Get referral data (API endpoint)
     */
    public function data()
    {
        $user = auth()->user();
        
        // Get total referrals
        $totalReferrals = $user->referrals()->count();
        
        // Get total referral earnings
        $totalEarnings = $user->referralEarnings()->sum('amount');
        
        // Get referral list with details
        $referrals = $user->referrals()
            ->with('membershipTier')
            ->get()
            ->map(function ($referral) use ($user) {
                $earnings = $user->referralEarnings()
                    ->where('referee_id', $referral->id)
                    ->sum('amount');
                
                return [
                    'id' => $referral->id,
                    'name' => $referral->name,
                    'initial' => strtoupper(substr($referral->name, 0, 1)),
                    'membership' => $referral->membershipTier->name,
                    'earnings' => '+' . number_format($earnings, 2) . ' USD',
                ];
            });
        
        return response()->json([
            'success' => true,
            'stats' => [
                'total_referrals' => $totalReferrals,
                'total_earnings' => number_format($totalEarnings, 2),
            ],
            'referrals' => $referrals,
        ]);
    }
}