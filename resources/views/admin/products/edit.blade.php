@extends('layouts.admin')

@section('title', 'Edit Product - ' . $product->name)

@section('content')
<div class="p-6">
    
    <!-- Back Button -->
    <div class="mb-6">
        <a href="{{ route('admin.products.index') }}" class="text-primary hover:text-primary/80 inline-flex items-center">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to Products
        </a>
    </div>

    <!-- Form Card -->
    <div class="max-w-2xl mx-auto bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Edit Product</h2>
            <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $product->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                {{ $product->is_active ? 'Active' : 'Inactive' }}
            </span>
        </div>

        <form action="{{ route('admin.products.update', $product) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Product Name *</label>
                <input type="text" name="name" value="{{ old('name', $product->name) }}" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent @error('name') border-red-500 @enderror">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea name="description" rows="4"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent @error('description') border-red-500 @enderror">{{ old('description', $product->description) }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Base Points *</label>
                    <div class="relative">
                        <input type="number" name="base_points" value="{{ old('base_points', $product->base_points) }}" required min="0" step="0.01"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent @error('base_points') border-red-500 @enderror">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <i class="fas fa-coins text-gray-400"></i>
                        </div>
                    </div>
                    @error('base_points')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Base Commission *</label>
                    <div class="relative">
                        <input type="number" name="base_commission" value="{{ old('base_commission', $product->base_commission) }}" required min="0" step="0.01"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent @error('base_commission') border-red-500 @enderror">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <i class="fas fa-dollar-sign text-gray-400"></i>
                        </div>
                    </div>
                    @error('base_commission')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Minimum Membership Tier *</label>
                <select name="min_membership_tier_id" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent @error('min_membership_tier_id') border-red-500 @enderror">
                    @foreach($membershipTiers as $tier)
                        <option value="{{ $tier->id }}" {{ old('min_membership_tier_id', $product->min_membership_tier_id) == $tier->id ? 'selected' : '' }}>
                            {{ $tier->name }} (Level {{ $tier->level }})
                        </option>
                    @endforeach
                </select>
                @error('min_membership_tier_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Image URL</label>
                <input type="url" name="image_url" value="{{ old('image_url', $product->image_url) }}"
                       placeholder="https://example.com/image.jpg"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent @error('image_url') border-red-500 @enderror">
                @error('image_url')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }}
                           class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">
                    <span class="ml-2 text-sm font-medium text-gray-700">Product is Active</span>
                </label>
                <p class="mt-1 text-xs text-gray-500">Inactive products cannot be assigned to users</p>
            </div>

            <!-- Stats -->
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                <h3 class="text-sm font-semibold text-gray-900 mb-3">Product Statistics</h3>
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <p class="text-xs text-gray-600">Total Submissions</p>
                        <p class="text-lg font-bold text-gray-900">{{ $product->total_submissions }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-600">In Queues</p>
                        <p class="text-lg font-bold text-gray-900">{{ $product->taskQueues()->queued()->count() }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-600">Active Tasks</p>
                        <p class="text-lg font-bold text-gray-900">{{ $product->tasks()->pending()->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                <a href="{{ route('admin.products.index') }}" 
                   class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                    Cancel
                </a>
                <button type="submit"
                        class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition">
                    <i class="fas fa-save mr-2"></i>
                    Update Product
                </button>
            </div>
        </form>
    </div>

</div>
@endsection