@extends('layouts.user')

@section('title', 'Recharge')

@section('content')
<div class="space-y-6">
    
    <!-- Page Title -->
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Recharge</h1>
    </div>

    <!-- Orange Divider Line -->
    <div class="w-full h-1 bg-gradient-to-r from-orange-500 to-orange-400"></div>

    <!-- Instructions -->
    <div class="bg-white rounded-2xl shadow-lg p-5 border border-gray-100">
        <p class="text-xs text-gray-600 mb-3">
            1. Please proceed with initiating the withdrawal process upon completion of all your daily orders.
        </p>
        <p class="text-xs text-gray-600">
            2. Our system algorithm ensures that the distribution of all products is conducted in a completely randomized manner, offering an equitable and unbiased process.
        </p>
    </div>

    <!-- Recharge Button -->
    <div class="flex justify-center py-4">
        <button @click="showCsPopup = true"
                class="w-full gradient-button max-w-xs text-white py-3 px-6 rounded-xl font-bold text-base shadow-lg transition">
            Recharge Now
        </button>
    </div>

    
    <!-- Customer Service Popup Component -->
    <div x-show="showCsPopup" 
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center px-4 py-4"
         style="background: rgba(0, 0, 0, 0.7);">
        
        <div class="bg-white rounded-3xl shadow-2xl max-w-md w-full overflow-hidden"
             x-transition:enter="transition ease-out duration-3000"
             x-transition:enter-start="opacity-0 transform translate-y-full"
             x-transition:enter-end="opacity-100 transform translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform translate-y-0"
             x-transition:leave-end="opacity-0 transform translate-y-full">
            
            <!-- Customer Service Options -->
            <div class="divide-y divide-gray-100">
                <!-- Online Customer Service -->
                <a href="#" 
                   class="flex items-center justify-between px-6 py-4 hover:bg-gray-50 transition group">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 rounded-full bg-orange-100 flex items-center justify-center">
                            <i class="fas fa-headset text-orange-500 text-xl"></i>
                        </div>
                        <span class="text-gray-900 font-medium text-base">Online Customer Service</span>
                    </div>
                    <svg class="w-6 h-6 text-gray-400 group-hover:text-orange-500 transition" 
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>

                <!-- Telegram CS -->
                <a href="#" 
                   class="flex items-center justify-between px-6 py-4 hover:bg-gray-50 transition group">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center">
                            <i class="fab fa-telegram-plane text-blue-500 text-xl"></i>
                        </div>
                        <span class="text-gray-900 font-medium text-base">Telegram CS</span>
                    </div>
                    <svg class="w-6 h-6 text-gray-400 group-hover:text-blue-500 transition" 
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            </div>

            <!-- Cancel Button -->
            <div class="p-4 bg-gray-50">
                <button @click="showCsPopup = false" 
                        class="w-full py-2 text-orange-500 font-semibold text-base hover:bg-white rounded-xl transition">
                    Cancel
                </button>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    function Recharge() {
        return {
            showCsPopup: false,

            onClickRecharge() {
                this.showCsPopup = true;
            }
        }
    }
</script>
@endpush