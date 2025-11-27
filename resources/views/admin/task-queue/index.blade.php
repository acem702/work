@extends('layouts.admin')

@section('title', 'Task Assignment')

@section('content')
<div class="p-6" x-data="taskAssignment()">
    
    <!-- Page Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Task Assignment</h1>
        <p class="text-gray-600 mt-1">Assign products to users or membership tiers</p>
    </div>

    <!-- Assignment Tabs -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6">
        <div class="border-b border-gray-200">
            <nav class="flex -mb-px">
                <button @click="activeTab = 'single'" 
                        :class="activeTab === 'single' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="px-6 py-4 border-b-2 font-medium text-sm transition">
                    Single User
                </button>
                <button @click="activeTab = 'multiple'" 
                        :class="activeTab === 'multiple' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="px-6 py-4 border-b-2 font-medium text-sm transition">
                    Multiple Users
                </button>
                <button @click="activeTab = 'tier'" 
                        :class="activeTab === 'tier' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="px-6 py-4 border-b-2 font-medium text-sm transition">
                    By Membership Tier
                </button>
            </nav>
        </div>

        <!-- Single User Assignment -->
        <div x-show="activeTab === 'single'" x-cloak class="p-6">
            <form @submit.prevent="assignToSingleUser" class="space-y-6">
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Select User</label>
                    <select x-model="singleUserId" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                        <option value="">Choose a user...</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">
                                {{ $user->name }} ({{ $user->email }}) - {{ $user->membershipTier->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Select Products</label>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 max-h-96 overflow-y-auto border border-gray-200 rounded-lg p-4">
                        @foreach($products as $product)
                            <label class="flex items-start space-x-3 p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                <input type="checkbox" 
                                       value="{{ $product->id }}"
                                       x-model="selectedProducts"
                                       class="mt-1 w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $product->name }}</p>
                                    <p class="text-xs text-gray-500">
                                        Points: {{ number_format($product->base_points) }} | 
                                        Commission: {{ number_format($product->base_commission) }}
                                    </p>
                                    <span class="inline-block mt-1 px-2 py-0.5 text-xs font-semibold rounded-full {{ membership_badge_color($product->minMembershipTier->level) }}">
                                        Min: {{ $product->minMembershipTier->name }}
                                    </span>
                                </div>
                            </label>
                        @endforeach
                    </div>
                    <p class="mt-2 text-sm text-gray-600">
                        <span x-text="selectedProducts.length"></span> product(s) selected
                    </p>
                </div>

                <div class="flex justify-end">
                    <button type="submit" 
                            :disabled="!singleUserId || selectedProducts.length === 0"
                            class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="fas fa-tasks mr-2"></i>
                        Assign Tasks
                    </button>
                </div>
            </form>
        </div>

        <!-- Multiple Users Assignment -->
        <div x-show="activeTab === 'multiple'" x-cloak class="p-6">
            <form @submit.prevent="assignToMultipleUsers" class="space-y-6">
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Select Users</label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 max-h-60 overflow-y-auto border border-gray-200 rounded-lg p-4">
                        @foreach($users as $user)
                            <label class="flex items-center space-x-3 p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                <input type="checkbox" 
                                       value="{{ $user->id }}"
                                       x-model="selectedUsers"
                                       class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900">{{ $user->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $user->email }}</p>
                                    <span class="inline-block mt-1 px-2 py-0.5 text-xs font-semibold rounded-full {{ membership_badge_color($user->membershipTier->level) }}">
                                        {{ $user->membershipTier->name }}
                                    </span>
                                </div>
                            </label>
                        @endforeach
                    </div>
                    <p class="mt-2 text-sm text-gray-600">
                        <span x-text="selectedUsers.length"></span> user(s) selected
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Select Products</label>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 max-h-96 overflow-y-auto border border-gray-200 rounded-lg p-4">
                        @foreach($products as $product)
                            <label class="flex items-start space-x-3 p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                <input type="checkbox" 
                                       value="{{ $product->id }}"
                                       x-model="selectedProducts"
                                       class="mt-1 w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $product->name }}</p>
                                    <p class="text-xs text-gray-500">
                                        Points: {{ number_format($product->base_points) }}
                                    </p>
                                    <span class="inline-block mt-1 px-2 py-0.5 text-xs font-semibold rounded-full {{ membership_badge_color($product->minMembershipTier->level) }}">
                                        Min: {{ $product->minMembershipTier->name }}
                                    </span>
                                </div>
                            </label>
                        @endforeach
                    </div>
                    <p class="mt-2 text-sm text-gray-600">
                        <span x-text="selectedProducts.length"></span> product(s) selected
                    </p>
                </div>

                <div class="flex justify-end">
                    <button type="submit" 
                            :disabled="selectedUsers.length === 0 || selectedProducts.length === 0"
                            class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="fas fa-tasks mr-2"></i>
                        Assign Tasks to <span x-text="selectedUsers.length"></span> User(s)
                    </button>
                </div>
            </form>
        </div>

        <!-- Tier Assignment -->
        <div x-show="activeTab === 'tier'" x-cloak class="p-6">
            <form @submit.prevent="assignToTier" class="space-y-6">
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Select Membership Tier</label>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @foreach($membershipTiers as $tier)
                            <label class="relative flex items-center p-4 border-2 rounded-lg cursor-pointer transition"
                                   :class="selectedTierLevel === {{ $tier->level }} ? 'border-primary bg-primary/5' : 'border-gray-200 hover:border-gray-300'">
                                <input type="radio" 
                                       value="{{ $tier->level }}"
                                       x-model="selectedTierLevel"
                                       class="sr-only">
                                <div class="flex-1">
                                    <p class="text-lg font-semibold text-gray-900">{{ $tier->name }}</p>
                                    <p class="text-sm text-gray-600">Level {{ $tier->level }}</p>
                                    <p class="text-xs text-gray-500 mt-1">
                                        {{ \App\Models\User::whereHas('membershipTier', fn($q) => $q->where('level', $tier->level))->count() }} users
                                    </p>
                                </div>
                                <div x-show="selectedTierLevel === {{ $tier->level }}" class="absolute top-2 right-2">
                                    <i class="fas fa-check-circle text-primary text-xl"></i>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Select Products</label>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 max-h-96 overflow-y-auto border border-gray-200 rounded-lg p-4">
                        @foreach($products as $product)
                            <label class="flex items-start space-x-3 p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                <input type="checkbox" 
                                       value="{{ $product->id }}"
                                       x-model="selectedProducts"
                                       class="mt-1 w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $product->name }}</p>
                                    <p class="text-xs text-gray-500">
                                        Points: {{ number_format($product->base_points) }}
                                    </p>
                                    <span class="inline-block mt-1 px-2 py-0.5 text-xs font-semibold rounded-full {{ membership_badge_color($product->minMembershipTier->level) }}">
                                        Min: {{ $product->minMembershipTier->name }}
                                    </span>
                                </div>
                            </label>
                        @endforeach
                    </div>
                    <p class="mt-2 text-sm text-gray-600">
                        <span x-text="selectedProducts.length"></span> product(s) selected
                    </p>
                </div>

                <div class="flex justify-end">
                    <button type="submit" 
                            :disabled="!selectedTierLevel || selectedProducts.length === 0"
                            class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="fas fa-tasks mr-2"></i>
                        Assign Tasks to Tier
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    function taskAssignment() {
        return {
            activeTab: 'single',
            singleUserId: '',
            selectedUsers: [],
            selectedProducts: [],
            selectedTierLevel: null,

            async assignToSingleUser() {
                if (!this.singleUserId || this.selectedProducts.length === 0) return;

                try {
                    const response = await axios.post('{{ route("admin.task-queue.assign.user") }}', {
                        user_id: this.singleUserId,
                        product_ids: this.selectedProducts
                    });

                    if (response.data.success) {
                        alert(response.data.message);
                        this.selectedProducts = [];
                        this.singleUserId = '';
                    }
                } catch (error) {
                    alert(error.response?.data?.message || 'Error assigning tasks');
                }
            },

            async assignToMultipleUsers() {
                if (this.selectedUsers.length === 0 || this.selectedProducts.length === 0) return;
                try {
                const response = await axios.post('{{ route("admin.task-queue.assign.users") }}', {
                    user_ids: this.selectedUsers,
                    product_ids: this.selectedProducts
                });

                if (response.data.success) {
                    alert(response.data.message);
                    this.selectedUsers = [];
                    this.selectedProducts = [];
                }
            } catch (error) {
                alert(error.response?.data?.message || 'Error assigning tasks');
            }
        },

        async assignToTier() {
            if (!this.selectedTierLevel || this.selectedProducts.length === 0) return;

            if (!confirm('This will assign tasks to all users in this tier. Continue?')) return;

            try {
                const response = await axios.post('{{ route("admin.task-queue.assign.tier") }}', {
                    tier_level: this.selectedTierLevel,
                    product_ids: this.selectedProducts
                });

                if (response.data.success) {
                    alert(response.data.message);
                    this.selectedProducts = [];
                    this.selectedTierLevel = null;
                }
            } catch (error) {
                alert(error.response?.data?.message || 'Error assigning tasks');
            }
        }
    }
}
</script>
@endpush
