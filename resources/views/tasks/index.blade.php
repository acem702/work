@extends('layouts.user')

@section('title', 'Access')

@section('content')
<div x-data="taskPage()" class="space-y-6">
    
    <!-- Page Title -->
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Access</h1>
    </div>

    <!-- Orange Divider Line -->
    <div class="w-full h-1 bg-gradient-to-r from-orange-500 to-orange-400"></div>

    <!-- Account Balance Card -->
    <div class="bg-gradient-to-br from-blue-900 via-blue-800 to-green-600 rounded-2xl shadow-xl p-6 text-center">
        <p class="text-sm text-gray-200 mb-2">Account Balance</p>
        <p class="text-4xl font-bold text-white mb-4">{{ number_format(auth()->user()->point_balance, 2) }} <span class="text-base">(USD)</span></p>
        <button @click="{{ route(name: 'recharge') }}" class="w-full max-w-xs mx-auto gradient-button text-white py-3 px-6 rounded-xl font-bold text-sm shadow-lg">
            Recharge
        </button>
    </div>

    <!-- Data Stats -->
    <div class="bg-white rounded-2xl shadow-lg p-6 text-center border border-gray-100">
        <p class="text-4xl font-bold text-gray-900 mb-2">{{ auth()->user()->tasks_completed_today }} / {{ auth()->user()->membershipTier->daily_task_limit }}</p>
        <p class="text-sm text-gray-600 uppercase">DATA</p>
    </div>

    <!-- Today's Earnings -->
    <div class="bg-white rounded-2xl shadow-lg p-6 text-center border border-gray-100">
        <p class="text-4xl font-bold text-gray-900 mb-2" x-text="todaysEarnings"></p>
        <p class="text-sm text-gray-600 uppercase">TODAY'S EARNINGS (USD)</p>
    </div>

    <!-- Frozen Amount -->
    <div class="bg-white rounded-2xl shadow-lg p-6 text-center border border-gray-100">
        <p class="text-4xl font-bold text-gray-900 mb-2" x-text="frozenAmount"></p>
        <p class="text-sm text-gray-600 uppercase">FROZEN AMOUNT (USD)</p>
    </div>

    <!-- Balance Due -->
    <div class="bg-white rounded-2xl shadow-lg p-6 text-center border border-gray-100">
        <p class="text-4xl font-bold text-gray-900 mb-2" x-text="balanceDue"></p>
        <p class="text-sm text-gray-600 uppercase">BALANCE DUE (USD)</p>
    </div>

    <!-- Orange Divider Line -->
    <div class="w-full h-1 bg-gradient-to-r from-orange-500 to-orange-400"></div>

    <!-- Begin Button -->
    <div class="flex justify-center py-4">
        <button @click="beginTask" 
                :disabled="!canStartTask"
                :class="canStartTask ? 'gradient-button' : 'bg-gray-300 cursor-not-allowed'"
                class="w-full max-w-xs text-white py-3 px-6 rounded-xl font-bold text-base shadow-lg transition">
            Begin
        </button>
    </div>

    <!-- Instructions -->
    <div class="bg-white rounded-2xl shadow-lg p-5 border border-gray-100">
        <p class="text-xs text-gray-600 mb-3">
            1. Please proceed with initiating the withdrawal process upon completion of all your daily orders.
        </p>
        <p class="text-xs text-gray-600">
            2. Our system algorithm ensures that the distribution of all products is conducted in a completely randomized manner, offering an equitable and unbiased process.
        </p>
    </div>

    <!-- Task Submission Modal -->
    <div x-show="showTaskModal" 
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center px-4"
         style="background: rgba(0, 0, 0, 0.6);">
        <div class="bg-white rounded-3xl shadow-2xl max-w-lg w-full p-6 relative"
             @click.away="showTaskModal = false"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform scale-90"
             x-transition:enter-end="opacity-100 transform scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform scale-100"
             x-transition:leave-end="opacity-0 transform scale-90">
            
            <!-- Close Button -->
            <button @click="showTaskModal = false" 
                    class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>

            <h2 class="text-xl font-bold text-gray-900 mb-6 text-center">Task Submission</h2>

            <div class="space-y-4">
                <!-- Product Image Placeholder -->
                <div class="flex items-start space-x-4">
                    <div class="w-24 h-24 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>

                    <!-- Product Details -->
                    <div class="flex-1">
                        <p class="text-sm text-gray-700 font-medium mb-2" x-text="currentTask?.product_name"></p>
                        <p class="text-sm text-gray-900 font-bold mb-2">USD <span x-text="currentTask?.base_points"></span></p>
                        
                        <!-- Star Rating -->
                        <div class="flex space-x-1">
                            <template x-for="i in 5" :key="i">
                                <svg class="w-5 h-5 text-yellow-400 fill-current" viewBox="0 0 20 20">
                                    <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                </svg>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Amount Details -->
                <div class="grid grid-cols-2 gap-4 py-4 border-t border-b border-gray-200">
                    <div class="text-center">
                        <p class="text-base font-bold text-gray-900 mb-1">Total Amount</p>
                        <p class="text-xs text-gray-500">USD <span class="text-orange-500 font-bold text-sm" x-text="currentTask?.base_points"></span></p>
                    </div>
                    <div class="text-center">
                        <p class="text-base font-bold text-gray-900 mb-1">Commission</p>
                        <p class="text-xs text-gray-500">USD <span class="text-orange-500 font-bold text-sm" x-text="currentTask?.commission"></span></p>
                    </div>
                </div>

                <!-- Task Info -->
                <div class="space-y-3 text-xs">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Created At</span>
                        <span class="text-gray-900 font-semibold" x-text="currentTask?.created_at"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Task Code</span>
                        <span class="text-orange-500 font-semibold text-xs" x-text="currentTask?.task_code"></span>
                    </div>
                </div>

                <!-- Submit Button -->
                <button @click="submitTask" 
                        class="w-full gradient-button text-white py-3 px-6 rounded-xl font-bold text-base shadow-lg mt-6">
                    Submit
                </button>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    function taskPage() {
        return {
            todaysEarnings: '0',
            frozenAmount: '0',
            balanceDue: '{{ number_format(auth()->user()->point_balance, 2) }}',
            canStartTask: true,
            showTaskModal: false,
            currentTask: null,
            processing: false,

            init() {
                this.fetchTaskStats();
            },

            async fetchTaskStats() {
                try {
                    const response = await axios.get('{{ route("api.tasks.stats") }}');
                    if (response.data) {
                        this.todaysEarnings = response.data.todaysEarnings || '0';
                        this.frozenAmount = response.data.frozenAmount || '0';
                        this.balanceDue = response.data.balanceDue || '0';
                    }
                } catch (error) {
                    console.error('Error fetching stats:', error);
                }
            },

            async beginTask() {
                if (!this.canStartTask || this.processing) return;

                this.processing = true;
                showLoading('Loading task...');

                try {
                    const response = await axios.get('{{ route("tasks.next") }}');
                    
                    hideLoading();
                    
                    if (response.data.success) {
                        const taskQueue = response.data.task_queue;
                        const product = taskQueue.product;
                        
                        this.currentTask = {
                            product_name: product.name,
                            base_points: parseFloat(product.base_points).toFixed(2),
                            commission: (product.base_commission * {{ auth()->user()->membershipTier->commission_multiplier }}).toFixed(2),
                            created_at: new Date().toLocaleString('en-US', {
                                year: 'numeric',
                                month: '2-digit',
                                day: '2-digit',
                                hour: '2-digit',
                                minute: '2-digit',
                                second: '2-digit',
                                hour12: false
                            }).replace(/(\d+)\/(\d+)\/(\d+),/, '$3-$1-$2'),
                            task_code: Date.now().toString() + Math.random().toString(36).substr(2, 9).toUpperCase(),
                            task_queue_id: taskQueue.id,
                            product_id: product.id
                        };
                        
                        this.showTaskModal = true;
                    } else {
                        showAlert(response.data.message || 'Unable to load task', 'error');
                    }
                } catch (error) {
                    hideLoading();
                    if (error.response?.data?.message) {
                        showAlert(error.response.data.message, 'error');
                    } else {
                        showAlert('Error loading task. Please try again.', 'error');
                    }
                } finally {
                    this.processing = false;
                }
            },

            async submitTask() {
                if (this.processing) return;

                this.processing = true;
                this.showTaskModal = false;
                showLoading('Processing task...');

                try {
                    // Start the task first (this will lock balance)
                    const startResponse = await axios.post('{{ route("tasks.start") }}', {
                        task_queue_id: this.currentTask.task_queue_id
                    });

                    if (startResponse.data.success) {
                        const task = startResponse.data.task;
                        
                        // Check if user can submit immediately
                        if (task.can_submit) {
                            // User has sufficient balance - submit immediately
                            const submitResponse = await axios.post('{{ route("tasks.submit") }}', {
                                task_id: task.id
                            });

                            hideLoading();

                            if (submitResponse.data.success) {
                                showAlert('Task completed successfully! +' + this.currentTask.commission + ' USD earned', 'success');
                                
                                // Reload page after 2 seconds
                                setTimeout(() => {
                                    window.location.reload();
                                }, 2000);
                            }
                        } else {
                            // Insufficient balance - task is pending, waiting for top-up
                            hideLoading();
                            showAlert('Task started but insufficient balance to submit. Your current balance has been locked. Please contact admin for top-up to complete this task.', 'warning');
                            
                            // Reload page after 3 seconds
                            setTimeout(() => {
                                window.location.reload();
                            }, 3000);
                        }
                    }
                } catch (error) {
                    hideLoading();
                    
                    if (error.response?.data?.message) {
                        showAlert(error.response.data.message, 'error');
                    } else {
                        showAlert('Error processing task. Please try again.', 'error');
                    }
                } finally {
                    this.processing = false;
                }
            }
        }
    }
</script>
@endpush