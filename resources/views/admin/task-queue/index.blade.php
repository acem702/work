@extends('layouts.admin')

@section('title', 'Task Assignment')

@section('content')
<div class="p-6" x-data="taskAssignment()">
    
    <!-- Page Header -->
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Task Assignment</h1>
            <p class="text-gray-600 mt-1">Assign products or combo tasks to users</p>
        </div>
        <a href="{{ route('admin.combo-tasks.index') }}" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
            <i class="fas fa-layer-group mr-2"></i>
            Manage Combo Tasks
        </a>
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

                <!-- Task Type Toggle -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Task Type</label>
                    <div class="flex space-x-4">
                        <button type="button" @click="taskType = 'products'" 
                                :class="taskType === 'products' ? 'bg-primary text-white' : 'bg-gray-100 text-gray-700'"
                                class="px-4 py-2 rounded-lg transition">
                            <i class="fas fa-box mr-2"></i>Regular Products
                        </button>
                        <button type="button" @click="taskType = 'combos'" 
                                :class="taskType === 'combos' ? 'bg-purple-600 text-white' : 'bg-gray-100 text-gray-700'"
                                class="px-4 py-2 rounded-lg transition">
                            <i class="fas fa-layer-group mr-2"></i>Combo Tasks
                        </button>
                    </div>
                </div>

                <!-- Regular Products Selection -->
                <div x-show="taskType === 'products'" x-cloak>
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

                <!-- Combo Tasks Selection -->
                <div x-show="taskType === 'combos'" x-cloak>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Select Combo Tasks</label>
                    <div class="space-y-3 max-h-96 overflow-y-auto border border-gray-200 rounded-lg p-4">
                        @forelse($comboTasks as $combo)
                            <label class="flex items-start space-x-3 p-4 border-2 border-purple-200 rounded-lg hover:bg-purple-50 cursor-pointer">
                                <input type="checkbox" 
                                       value="{{ $combo->id }}"
                                       x-model="selectedCombos"
                                       class="mt-1 w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between mb-2">
                                        <p class="text-base font-bold text-gray-900">{{ $combo->name }}</p>
                                        <span class="px-2 py-1 bg-purple-100 text-purple-700 text-xs font-semibold rounded-full">
                                            {{ $combo->sequence_count }} Tasks
                                        </span>
                                    </div>
                                    @if($combo->description)
                                        <p class="text-sm text-gray-600 mb-2">{{ $combo->description }}</p>
                                    @endif
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($combo->items as $item)
                                            <span class="inline-flex items-center px-2 py-1 bg-white border border-gray-200 rounded text-xs">
                                                <span class="w-4 h-4 bg-purple-600 text-white rounded-full flex items-center justify-center text-xs mr-1">
                                                    {{ $item->sequence_order }}
                                                </span>
                                                {{ $item->product->name }} ({{ number_format($item->product->base_points) }})
                                            </span>
                                        @endforeach
                                    </div>
                                    <p class="text-xs text-gray-500 mt-2">
                                        Total Points: <strong>{{ number_format($combo->total_base_points) }}</strong>
                                    </p>
                                </div>
                            </label>
                        @empty
                            <div class="text-center py-8 text-gray-500">
                                <i class="fas fa-layer-group text-4xl mb-2"></i>
                                <p>No combo tasks available</p>
                                <a href="{{ route('admin.combo-tasks.index') }}" class="text-purple-600 hover:text-purple-700 text-sm mt-2 inline-block">
                                    Create Combo Task
                                </a>
                            </div>
                        @endforelse
                    </div>
                    <p class="mt-2 text-sm text-gray-600" x-show="taskType === 'combos'">
                        <span x-text="selectedCombos.length"></span> combo task(s) selected
                    </p>
                </div>

                <div class="flex justify-end">
                    <button type="submit" 
                            :disabled="!singleUserId || (taskType === 'products' && selectedProducts.length === 0) || (taskType === 'combos' && selectedCombos.length === 0)"
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

                <!-- Task Type Toggle -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Task Type</label>
                    <div class="flex space-x-4">
                        <button type="button" @click="taskType = 'products'" 
                                :class="taskType === 'products' ? 'bg-primary text-white' : 'bg-gray-100 text-gray-700'"
                                class="px-4 py-2 rounded-lg transition">
                            <i class="fas fa-box mr-2"></i>Regular Products
                        </button>
                        <button type="button" @click="taskType = 'combos'" 
                                :class="taskType === 'combos' ? 'bg-purple-600 text-white' : 'bg-gray-100 text-gray-700'"
                                class="px-4 py-2 rounded-lg transition">
                            <i class="fas fa-layer-group mr-2"></i>Combo Tasks
                        </button>
                    </div>
                </div>

                <!-- Regular Products or Combos based on taskType -->
                <div x-show="taskType === 'products'" x-cloak>
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
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div x-show="taskType === 'combos'" x-cloak>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Select Combo Tasks</label>
                    <div class="space-y-3 max-h-96 overflow-y-auto border border-gray-200 rounded-lg p-4">
                        @foreach($comboTasks as $combo)
                            <label class="flex items-start space-x-3 p-4 border-2 border-purple-200 rounded-lg hover:bg-purple-50 cursor-pointer">
                                <input type="checkbox" 
                                       value="{{ $combo->id }}"
                                       x-model="selectedCombos"
                                       class="mt-1 w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                                <div class="flex-1 min-w-0">
                                    <p class="text-base font-bold text-gray-900">{{ $combo->name }}</p>
                                    <div class="flex flex-wrap gap-2 mt-2">
                                        @foreach($combo->items as $item)
                                            <span class="text-xs bg-white border border-gray-200 rounded px-2 py-1">
                                                {{ $item->sequence_order }}. {{ $item->product->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" 
                            :disabled="selectedUsers.length === 0 || (taskType === 'products' && selectedProducts.length === 0) || (taskType === 'combos' && selectedCombos.length === 0)"
                            class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="fas fa-tasks mr-2"></i>
                        Assign to <span x-text="selectedUsers.length"></span> User(s)
                    </button>
                </div>
            </form>
        </div>

        <!-- Tier Assignment (similar structure) -->
        <div x-show="activeTab === 'tier'" x-cloak class="p-6">
            <!-- Similar to multiple users but with tier selection -->
        </div>

    </div>

</div>
@endsection

@push('scripts')
<script>
    function taskAssignment() {
        return {
            activeTab: 'single',
            taskType: 'products',
            singleUserId: '',
            selectedUsers: [],
            selectedProducts: [],
            selectedCombos: [],
            selectedTierLevel: null,

            async assignToSingleUser() {
                if (!this.singleUserId) return;

                const payload = {
                    user_id: this.singleUserId
                };

                if (this.taskType === 'products' && this.selectedProducts.length > 0) {
                    payload.product_ids = this.selectedProducts;
                } else if (this.taskType === 'combos' && this.selectedCombos.length > 0) {
                    payload.combo_task_ids = this.selectedCombos;
                } else {
                    return;
                }

                try {
                    const response = await axios.post('{{ route("admin.task-queue.assign.user") }}', payload);

                    if (response.data.success) {
                        alert(response.data.message);
                        this.selectedProducts = [];
                        this.selectedCombos = [];
                        this.singleUserId = '';
                    }
                } catch (error) {
                    alert(error.response?.data?.message || 'Error assigning tasks');
                }
            },

            async assignToMultipleUsers() {
                if (this.selectedUsers.length === 0) return;

                const payload = {
                    user_ids: this.selectedUsers
                };

                if (this.taskType === 'products' && this.selectedProducts.length > 0) {
                    payload.product_ids = this.selectedProducts;
                } else if (this.taskType === 'combos' && this.selectedCombos.length > 0) {
                    payload.combo_task_ids = this.selectedCombos;
                } else {
                    return;
                }

                try {
                    const response = await axios.post('{{ route("admin.task-queue.assign.users") }}', payload);

                    if (response.data.success) {
                        alert(response.data.message);
                        this.selectedUsers = [];
                        this.selectedProducts = [];
                        this.selectedCombos = [];
                    }
                } catch (error) {
                    alert(error.response?.data?.message || 'Error assigning tasks');
                }
            },

            async assignToTier() {
                if (!this.selectedTierLevel) return;

                const payload = {
                    tier_level: this.selectedTierLevel
                };

                if (this.taskType === 'products' && this.selectedProducts.length > 0) {
                    payload.product_ids = this.selectedProducts;
                } else if (this.taskType === 'combos' && this.selectedCombos.length > 0) {
                    payload.combo_task_ids = this.selectedCombos;
                } else {
                    return;
                }

                if (!confirm('This will assign tasks to all users in this tier. Continue?')) return;

                try {
                    const response = await axios.post('{{ route("admin.task-queue.assign.tier") }}', payload);

                    if (response.data.success) {
                        alert(response.data.message);
                        this.selectedProducts = [];
                        this.selectedCombos = [];
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