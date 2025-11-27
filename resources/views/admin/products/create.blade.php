@extends('layouts.admin')

@section('title', 'Create Product')

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
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Create New Product</h2>
        <form action="{{ route('admin.products.store') }}" method="POST" class="space-y-6">
        @csrf

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Product Name *</label>
            <input type="text" name="name" value="{{ old('name') }}" required
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent @error('name') border-red-500 @enderror">
            @error('name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
            <textarea name="description" rows="4"
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
            @error('description')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Base Points *</label>
                <div class="relative">
                    <input type="number" name="base_points" value="{{ old('base_points') }}" required min="0" step="0.01"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent @error('base_points') border-red-500 @enderror">
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                        <i class="fas fa-coins text-gray-400"></i>
                    </div>
                </div>
                <p class="mt-1 text-xs text-gray-500">Points required to perform this task</p>
                @error('base_points')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Base Commission *</label>
                <div class="relative">
                    <input type="number" name="base_commission" value="{{ old('base_commission') }}" required min="0" step="0.01"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent @error('base_commission') border-red-500 @enderror">
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                        <i class="fas fa-dollar-sign text-gray-400"></i>
                    </div>
                </div>
                <p class="mt-1 text-xs text-gray-500">Base commission earned (before tier multiplier)</p>
                @error('base_commission')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Minimum Membership Tier *</label>
            <select name="min_membership_tier_id" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent @error('min_membership_tier_id') border-red-500 @enderror">
                <option value="">Select tier...</option>
                @foreach($membershipTiers as $tier)
                    <option value="{{ $tier->id }}" {{ old('min_membership_tier_id') == $tier->id ? 'selected' : '' }}>
                        {{ $tier->name }} (Level {{ $tier->level }})
                    </option>
                @endforeach
            </select>
            <p class="mt-1 text-xs text-gray-500">Users must have this tier or higher to access this task</p>
            @error('min_membership_tier_id')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Image URL</label>
            <input type="url" name="image_url" value="{{ old('image_url') }}"
                   placeholder="https://example.com/image.jpg"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent @error('image_url') border-red-500 @enderror">
            <p class="mt-1 text-xs text-gray-500">Optional: URL to product image</p>
            @error('image_url')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Commission Preview -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <h3 class="text-sm font-semibold text-blue-900 mb-3">Commission Preview by Tier</h3>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                @foreach($membershipTiers as $tier)
                    <div class="bg-white rounded p-3">
                        <p class="text-xs text-gray-600">{{ $tier->name }}</p>
                        <p class="text-sm font-bold text-gray-900">
                            <span class="commission-preview" data-multiplier="{{ $tier->commission_multiplier }}">0</span> pts
                        </p>
                        <p class="text-xs text-gray-500">×{{ $tier->commission_multiplier }}</p>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
            <a href="{{ route('admin.products.index') }}" 
               class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                Cancel
            </a>
            <button type="submit"
                    class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition">
                <i class="fas fa-check mr-2"></i>
                Create Product
            </button>
        </div>
    </form>
</div>
</div>
@endsection
@push('scripts')
<script>
    // Update commission preview when base commission changes
    document.querySelector('input[name="base_commission"]').addEventListener('input', function(e) {
        const baseCommission = parseFloat(e.target.value) || 0;
        document.querySelectorAll('.commission-preview').forEach(el => {
            const multiplier = parseFloat(el.dataset.multiplier);
            el.textContent = (baseCommission * multiplier).toFixed(2);
        });
    });
</script>
@endpush