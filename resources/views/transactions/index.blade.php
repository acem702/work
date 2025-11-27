@extends('layouts.user')

@section('title', 'Bonus History')

@section('content')
<div x-data="transactionPage()" class="space-y-4">
    
    <!-- Page Title -->
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Bonus History</h1>
    </div>

    <!-- Orange Divider Line -->
    <div class="w-full h-1 bg-gradient-to-r from-orange-500 to-orange-400"></div>

    <!-- Filter Tabs -->
    <div class="flex bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <button @click="activeFilter = 'recharge'" 
                :class="activeFilter === 'recharge' ? 'border-b-2 border-orange-500 text-gray-900 font-bold' : 'text-gray-500'"
                class="flex-1 py-3 text-sm transition">
            Recharge
        </button>
        <button @click="activeFilter = 'withdrawal'" 
                :class="activeFilter === 'withdrawal' ? 'border-b-2 border-orange-500 text-gray-900 font-bold' : 'text-gray-500'"
                class="flex-1 py-3 text-sm transition">
            Withdrawal
        </button>
        <button @click="activeFilter = 'commission'" 
                :class="activeFilter === 'commission' ? 'border-b-2 border-orange-500 text-gray-900 font-bold' : 'text-gray-500'"
                class="flex-1 py-3 text-sm transition">
            Commission History
        </button>
    </div>

    <!-- Transactions List -->
    <div class="space-y-4">
        <template x-for="transaction in filteredTransactions" :key="transaction.id">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                <!-- Transaction Details -->
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600 font-medium">ORDER NUMBER:</span>
                        <span class="text-gray-900 font-mono text-xs break-all max-w-[60%] text-right" x-text="transaction.order_number"></span>
                    </div>
                    
                    <div class="flex justify-between">
                        <span class="text-gray-600 font-medium">AMOUNT:</span>
                        <span class="font-bold" 
                              :class="transaction.amount >= 0 ? 'text-green-600' : 'text-red-600'"
                              x-text="transaction.amount"></span>
                    </div>
                    
                    <template x-if="transaction.status">
                        <div class="flex justify-between">
                            <span class="text-gray-600 font-medium">STATUS:</span>
                            <span class="font-bold" 
                                  :class="transaction.status === 'SUCCESS' ? 'text-green-600' : 'text-yellow-600'"
                                  x-text="transaction.status"></span>
                        </div>
                    </template>
                    
                    <template x-if="transaction.commission_type">
                        <div class="flex justify-between">
                            <span class="text-gray-600 font-medium">COMMISSION:</span>
                            <span class="text-gray-900 font-semibold" x-text="transaction.commission_type"></span>
                        </div>
                    </template>
                    
                    <div class="flex justify-between pt-2 border-t border-gray-200">
                        <span class="text-gray-600 font-medium">CREATE TIME:</span>
                        <span class="text-gray-500 text-xs" x-text="transaction.created_at"></span>
                    </div>
                </div>
            </div>
        </template>

        <!-- No Data -->
        <template x-if="filteredTransactions.length === 0">
            <div class="py-16 text-center">
                <p class="text-gray-400 text-sm">No Data Available</p>
            </div>
        </template>
    </div>

</div>
@endsection

@push('scripts')
<script>
    function transactionPage() {
        return {
            activeFilter: 'recharge',
            transactions: [],
            loading: true,

            init() {
                this.fetchTransactions();
            },

            get filteredTransactions() {
                return this.transactions.filter(tx => tx.type === this.activeFilter);
            },

            async fetchTransactions() {
                try {
                    showLoading('Loading transactions...');
                    const response = await axios.get('{{ route("transactions.history") }}');
                    
                    if (response.data.success) {
                        this.transactions = response.data.transactions;
                    }
                    
                    hideLoading();
                } catch (error) {
                    hideLoading();
                    console.error('Error fetching transactions:', error);
                    showAlert('Error loading transactions', 'error');
                }
            }
        }
    }
</script>
@endpush