<?php

namespace App\Http\Controllers;

use App\Services\MembershipService;
use App\Models\MembershipTier;
use Illuminate\Http\Request;
use Exception;

class MembershipTierController extends Controller
{
    protected $membershipService;

    public function __construct(MembershipService $membershipService)
    {
        $this->membershipService = $membershipService;
    }

    /**
     * Display membership page
     */
    public function index()
    {
        $user = auth()->user();
        $currentTier = $user->membershipTier;
        $availableUpgrades = $this->membershipService->getAvailableUpgrades($user);

        return view('membership.index', compact('currentTier', 'availableUpgrades'));
    }

    /**
     * Upgrade membership
     */
    public function upgrade(Request $request)
    {
        $request->validate([
            'tier_id' => 'required|exists:membership_tiers,id',
        ]);

        try {
            $user = auth()->user();
            $newTier = MembershipTier::findOrFail($request->tier_id);
            
            $this->membershipService->upgradeMembership($user, $newTier);

            return response()->json([
                'success' => true,
                'message' => "Successfully upgraded to {$newTier->name}!",
                'new_balance' => $user->fresh()->point_balance,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}