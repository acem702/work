<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\MembershipTier;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    /**
     * Display products list
     */
    public function index()
    {
        $products = Product::with('minMembershipTier')->latest()->paginate(20);
        return view('admin.products.index', compact('products'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        $membershipTiers = MembershipTier::active()->ordered()->get();
        return view('admin.products.create', compact('membershipTiers'));
    }

    /**
     * Store new product
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'base_points' => 'required|numeric|min:0',
            'base_commission' => 'required|numeric|min:0',
            'min_membership_tier_id' => 'required|exists:membership_tiers,id',
            'image_url' => 'nullable|url',
        ]);

        $product = Product::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name) . '-' . time(),
            'description' => $request->description,
            'base_points' => $request->base_points,
            'base_commission' => $request->base_commission,
            'min_membership_tier_id' => $request->min_membership_tier_id,
            'image_url' => $request->image_url,
        ]);

        return redirect()->route('admin.products.index')
            ->with('success', 'Product created successfully');
    }

    /**
     * Show edit form
     */
    public function edit($id)
    {
        $product = Product::findOrFail($id);
        $membershipTiers = MembershipTier::active()->ordered()->get();
        return view('admin.products.edit', compact('product', 'membershipTiers'));
    }

    /**
     * Update product
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'base_points' => 'required|numeric|min:0',
            'base_commission' => 'required|numeric|min:0',
            'min_membership_tier_id' => 'required|exists:membership_tiers,id',
            'image_url' => 'nullable|url',
            'is_active' => 'boolean',
        ]);

        $product = Product::findOrFail($id);
        $product->update($request->all());

        return redirect()->route('admin.products.index')
            ->with('success', 'Product updated successfully');
    }

    /**
     * Delete product
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully',
        ]);
    }
}