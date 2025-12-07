<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MembershipTier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Exception;

class MembershipTierController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    /**
     * Display membership tiers management page
     */
    public function index()
    {
        $tiers = MembershipTier::withCount(['users' => function ($query) {
            $query->where('status', 'active');
        }])->ordered()->get();

        return view('admin.membership-tiers.index', compact('tiers'));
    }

    /**
     * Store a new membership tier
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'level' => 'required|integer|min:1|unique:membership_tiers,level',
            'daily_task_limit' => 'required|integer|min:1',
            'commission_multiplier' => 'required|numeric|min:0.1',
            'upgrade_cost' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        ]);

        try {
            $data = [
                'name' => $request->name,
                'level' => $request->level,
                'daily_task_limit' => $request->daily_task_limit,
                'commission_multiplier' => $request->commission_multiplier,
                'upgrade_cost' => $request->upgrade_cost,
                'description' => $request->description,
                'is_active' => true,
            ];

            // Handle image upload
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $path = $image->storeAs('membership-tiers', $filename, 'public');
                $data['image_url'] = $path;
            }

            $tier = MembershipTier::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Membership tier created successfully',
                'tier' => $tier,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Update membership tier
     */
    public function update(Request $request, MembershipTier $membershipTier)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'level' => 'required|integer|min:1|unique:membership_tiers,level,' . $membershipTier->id,
            'daily_task_limit' => 'required|integer|min:1',
            'commission_multiplier' => 'required|numeric|min:0.1',
            'upgrade_cost' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'remove_image' => 'nullable|boolean',
        ]);

        try {
            $data = [
                'name' => $request->name,
                'level' => $request->level,
                'daily_task_limit' => $request->daily_task_limit,
                'commission_multiplier' => $request->commission_multiplier,
                'upgrade_cost' => $request->upgrade_cost,
                'description' => $request->description,
            ];

            // Handle image removal
            if ($request->remove_image) {
                if ($membershipTier->image_url && Storage::disk('public')->exists($membershipTier->image_url)) {
                    Storage::disk('public')->delete($membershipTier->image_url);
                }
                $data['image_url'] = null;
            }

            // Handle new image upload
            if ($request->hasFile('image')) {
                // Delete old image
                if ($membershipTier->image_url && Storage::disk('public')->exists($membershipTier->image_url)) {
                    Storage::disk('public')->delete($membershipTier->image_url);
                }

                // Upload new image
                $image = $request->file('image');
                $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $path = $image->storeAs('membership-tiers', $filename, 'public');
                $data['image_url'] = $path;
            }

            $membershipTier->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Membership tier updated successfully',
                'tier' => $membershipTier->fresh(),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Delete membership tier
     */
    public function destroy(MembershipTier $membershipTier)
    {
        try {
            // Check if tier has active users
            $activeUsersCount = $membershipTier->users()->where('status', 'active')->count();
            
            if ($activeUsersCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => "Cannot delete tier with {$activeUsersCount} active users. Please reassign users first.",
                ], 400);
            }

            // Check if tier is referenced by products
            $productsCount = $membershipTier->products()->count();
            
            if ($productsCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => "Cannot delete tier referenced by {$productsCount} products. Please update products first.",
                ], 400);
            }

            // Delete image if exists
            if ($membershipTier->image_url && Storage::disk('public')->exists($membershipTier->image_url)) {
                Storage::disk('public')->delete($membershipTier->image_url);
            }

            $membershipTier->delete();

            return response()->json([
                'success' => true,
                'message' => 'Membership tier deleted successfully',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Toggle tier status
     */
    public function toggleStatus(MembershipTier $membershipTier)
    {
        try {
            $membershipTier->update(['is_active' => !$membershipTier->is_active]);

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully',
                'is_active' => $membershipTier->is_active,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}