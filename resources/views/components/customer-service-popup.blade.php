<div x-data="{ localOpen: false }" 
     x-show="$store.csPopup?.isOpen ?? localOpen" 
     x-cloak
     class="fixed inset-0 z-[60] flex items-end justify-center px-4 pb-4"
     style="background: rgba(0, 0, 0, 0.4);"
     @click.self="$store.csPopup?.close(); localOpen = false">
    
    <div class="bg-white rounded-t-3xl shadow-2xl max-w-md w-full overflow-hidden"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform translate-y-full"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 transform translate-y-0"
         x-transition:leave-end="opacity-0 transform translate-y-full">
        
        <!-- Customer Service Options -->
        <div class="divide-y divide-gray-100">
            <!-- Online Customer Service -->
            <a href="{{ route('customer-service.online') }}" 
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
            <a href="{{ route('customer-service.telegram') }}" 
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
            <button @click="$store.csPopup?.close(); localOpen = false" 
                    class="w-full py-3 text-orange-500 font-semibold text-base hover:bg-white rounded-xl transition">
                Cancel
            </button>
        </div>
    </div>
</div>