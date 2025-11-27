@extends('layouts.admin')

@section('title', 'Products Management')

@section('content')
<div class="p-6">
    
    <!-- Page Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Products Management</h1>
            <p class="text-gray-600 mt-1">Manage all task products</p>
        </div>
        <a href="{{ route('admin.products.create') }}" 
           class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition flex items-center">
            <i class="fas fa-plus mr-2"></i>
            Create Product
        </a>
    </div>

    <!-- Products Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($products as $product)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition">
                
                <!-- Product Image -->
                <div class="h-40 bg-gradient-to-br from-primary to-secondary flex items-center justify-center">
                    @if($product->image_url)
                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                    @else
                        <i class="fas fa-box text-white text-5xl"></i>
                    @endif
                </div>

                <!-- Product Info -->
                <div class="p-6">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-900">{{ $product->name }}</h3>
                            <p class="text-sm text-gray-600 mt-1 line-clamp-2">{{ $product->description }}</p>
                        </div>
                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $product->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $product->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>

                    <!-- Stats -->
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div class="bg-blue-50 rounded-lg p-3">
                            <p class="text-xs text-gray-600">Base Points</p>
                            <p class="text-lg font-bold text-blue-600">{{ number_format($product->base_points) }}</p>
                        </div>
                        <div class="bg-green-50 rounded-lg p-3">
                            <p class="text-xs text-gray-600">Commission</p>
                            <p class="text-lg font-bold text-green-600">{{ number_format($product->base_commission) }}</p>
                        </div>
                    </div>

                    <!-- Requirements -->
                    <div class="mb-4">
                        <p class="text-xs text-gray-600 mb-2">Minimum Membership</p>
                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ membership_badge_color($product->minMembershipTier->level) }}">
                            {{ $product->minMembershipTier->name }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between text-sm text-gray-600 mb-4">
                        <span><i class="fas fa-tasks mr-1"></i> {{ $product->total_submissions }} submissions</span>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center space-x-2">
                        <a href="{{ route('admin.products.edit', $product) }}" 
                           class="flex-1 px-4 py-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition text-center text-sm font-medium">
                            <i class="fas fa-edit mr-1"></i> Edit
                        </a>
                        <button onclick="deleteProduct({{ $product->id }})" 
                                class="px-4 py-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition text-sm font-medium">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-3 bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
                <i class="fas fa-box text-6xl text-gray-300 mb-4"></i>
                <p class="text-gray-500 text-lg">No products found</p>
                <a href="{{ route('admin.products.create') }}" class="mt-4 inline-block text-primary hover:text-primary/80">
                    Create your first product
                </a>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($products->hasPages())
        <div class="mt-6">
            {{ $products->links() }}
        </div>
    @endif

</div>
@endsection

@push('scripts')
<script>
    async function deleteProduct(productId) {
        if (!confirm('Are you sure you want to delete this product?')) return;
        
        try {
            const response = await axios.delete(`/admin/products/${productId}`);
            if (response.data.success) {
                location.reload();
            }
        } catch (error) {
            alert(error.response?.data?.message || 'Error deleting product');
        }
    }
</script>
@endpush