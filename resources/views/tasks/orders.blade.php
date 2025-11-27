@extends('layouts.user')

@section('title', 'Orders')

@section('content')
<div x-data="ordersPage()" class="space-y-4">
    
    <!-- Page Title -->
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Orders</h1>
    </div>

    <!-- Orange Divider Line -->
    <div class="w-full h-1 bg-gradient-to-r from-orange-500 to-orange-400"></div>

    <!-- Filter Tabs -->
    <div class="flex bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <button @click="activeFilter = 'all'" 
                :class="activeFilter === 'all' ? 'border-b-2 border-orange-500 text-gray-900 font-bold' : 'text-gray-500'"
                class="flex-1 py-3 text-sm transition">
            All
        </button>
        <button @click="activeFilter = 'pending'" 
                :class="activeFilter === 'pending' ? 'border-b-2 border-orange-500 text-gray-900 font-bold' : 'text-gray-500'"
                class="flex-1 py-3 text-sm transition">
            Pending
        </button>
        <button @click="activeFilter = 'completed'" 
                :class="activeFilter === 'completed' ? 'border-b-2 border-orange-500 text-gray-900 font-bold' : 'text-gray-500'"
                class="flex-1 py-3 text-sm transition">
            Completed
        </button>
    </div>

    <!-- Orders List -->
    <div class="space-y-4">
        <template x-for="task in filteredTasks" :key="task.id">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                <!-- Date and Status -->
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs text-gray-500" x-text="task.created_at"></span>
                    <span class="px-3 py-1 text-xs font-bold rounded-full" 
                          :class="task.status === 'completed' ? 'bg-orange-500 text-white' : 'bg-yellow-500 text-white'"
                          x-text="task.status_label">
                    </span>
                </div>

                <!-- Product Details -->
                <div class="flex items-start space-x-3 mb-3">
                    <!-- Product Image -->
                    <div class="w-16 h-16 bg-gray-100 rounded-lg flex-shrink-0 overflow-hidden">
                        <template x-if="task.product_image">
                            <img :src="task.product_image" :alt="task.product_name" class="w-full h-full object-cover">
                        </template>
                        <template x-if="!task.product_image">
                            <div class="w-full h-full flex items-center justify-center">
                                <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                        </template>
                    </div>

                    <!-- Product Info -->
                    <div class="flex-1 min-w-0">
                        <h3 class="text-sm font-semibold text-gray-900 mb-1 line-clamp-2" x-text="task.product_name"></h3>
                        <p class="text-sm font-bold text-gray-900 mb-2">USD <span x-text="task.total_amount"></span></p>
                        
                        <!-- Star Rating -->
                        <div class="flex space-x-0.5">
                            <template x-for="i in 5" :key="i">
                                <svg class="w-4 h-4 text-yellow-400 fill-current" viewBox="0 0 20 20">
                                    <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                </svg>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Task Stats Grid -->
                <div class="grid grid-cols-3 gap-3 mb-3">
                    <div class="text-center">
                        <p class="text-xs text-gray-500 mb-1">Total Amount</p>
                        <p class="text-sm font-bold text-orange-500">USD <span x-text="task.total_amount"></span></p>
                    </div>
                    <div class="text-center">
                        <p class="text-xs text-gray-500 mb-1">Commission</p>
                        <p class="text-sm font-bold text-orange-500">USD <span x-text="task.commission"></span></p>
                    </div>
                    <div class="text-center">
                        <p class="text-xs text-gray-500 mb-1">Task Progress</p>
                        <p class="text-sm font-bold text-yellow-500" x-text="task.task_progress"></p>
                    </div>
                </div>

                <!-- Submit Button for Pending Tasks -->
                <template x-if="task.status === 'pending' && task.can_submit">
                    <button @click="confirmSubmit(task)" 
                            class="w-full gradient-button text-white py-2.5 px-4 rounded-lg font-bold text-sm shadow-lg">
                        Submit Order
                    </button>
                </template>

                <!-- Waiting for Top-up Message -->
                <template x-if="task.status === 'pending' && !task.can_submit">
                    <div class="bg-yellow-50 border border-yellow-300 rounded-lg p-2.5 text-center">
                        <p class="text-xs text-yellow-800 font-medium">
                            <i class="fas fa-exclamation-circle mr-1"></i>
                            Waiting for admin top-up to submit
                        </p>
                    </div>
                </template>
            </div>
        </template>

        <!-- No Data -->
        <template x-if="filteredTasks.length === 0">
            <div class="py-16 text-center">
                <p class="text-gray-400 text-sm">No more data...</p>
            </div>
        </template>
    </div>

    <!-- Custom Confirmation Dialog -->
    <div x-show="showConfirmDialog" 
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center px-4"
         style="background: rgba(0, 0, 0, 0.6);">
        <div class="bg-white rounded-2xl shadow-2xl max-w-sm w-full p-6 text-center"
             @click.away="showConfirmDialog = false"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform scale-90"
             x-transition:enter-end="opacity-100 transform scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform scale-100"
             x-transition:leave-end="opacity-0 transform scale-90">
            
            <h3 class="text-base font-bold text-gray-900 mb-3">Submit Order</h3>
            <p class="text-gray-600 text-sm mb-6">Are you sure you want to submit this order?</p>
            
            <div class="flex space-x-3">
                <button @click="showConfirmDialog = false" 
                        class="flex-1 px-4 py-2.5 border-2 border-gray-300 text-gray-700 rounded-xl font-semibold text-sm hover:bg-gray-50 transition">
                    Cancel
                </button>
                <button @click="submitPendingTask()" 
                        class="flex-1 px-4 py-2.5 bg-gradient-to-r from-orange-500 to-red-500 text-white rounded-xl font-semibold text-sm hover:shadow-lg transition">
                    Confirm
                </button>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    function ordersPage() {
        return {
            activeFilter: 'all',
            tasks: [],
            loading: true,
            showConfirmDialog: false,
            selectedTask: null,

            init() {
                this.fetchTasks();
            },

            get filteredTasks() {
                if (this.activeFilter === 'all') {
                    return this.tasks;
                }
                return this.tasks.filter(task => task.status === this.activeFilter);
            },

            async fetchTasks() {
                try {
                    showLoading('Loading orders...');
                    const response = await axios.get('{{ route("tasks.history") }}');
                    
                    if (response.data.success) {
                        this.tasks = response.data.tasks;
                    }
                    
                    hideLoading();
                } catch (error) {
                    hideLoading();
                    console.error('Error fetching tasks:', error);
                    showAlert('Error loading orders', 'error');
                }
            },

            confirmSubmit(task) {
                this.selectedTask = task;
                this.showConfirmDialog = true;
            },

            async submitPendingTask() {
                if (!this.selectedTask) return;

                const taskId = this.selectedTask.id;
                this.showConfirmDialog = false;

                try {
                    showLoading('Submitting order...');
                    
                    const response = await axios.post('{{ route("tasks.submit") }}', {
                        task_id: taskId
                    });

                    hideLoading();

                    if (response.data.success) {
                        showAlert('Order completed successfully! Commission earned: +' + response.data.task.commission_earned + ' USD', 'success');
                        
                        // Refresh tasks after 2 seconds
                        setTimeout(() => {
                            this.fetchTasks();
                        }, 2000);
                    }
                } catch (error) {
                    hideLoading();
                    
                    if (error.response?.data?.message) {
                        showAlert(error.response.data.message, 'error');
                    } else {
                        showAlert('Error submitting order', 'error');
                    }
                } finally {
                    this.selectedTask = null;
                }
            }
        }
    }
</script>
@endpush