@extends('layouts.user')

@section('title', 'Premium Membership')

@section('content')
<div x-data="membershipPage()" class="space-y-6">
    
    <!-- Page Title -->
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900">Premium Membership</h1>

        <a href="{{ route('dashboard') }}" 
        class="flex items-center space-x-2 px-4 py-2 bg-gradient-to-r from-orange-500 to-red-500 rounded-xl text-white text-sm font-bold shadow-lg hover:shadow-xl transition">
            <i class="fas fa-arrow-left"></i>
            <span>Dashboard</span>
        </a>
    </div>

    <!-- Orange Divider Line -->
    <div class="w-full h-1 bg-gradient-to-r from-orange-500 to-orange-400"></div>

    <!-- Main Card -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
        
        <!-- Membership Icon -->
        <div class="flex justify-center mb-6">
            <div class="w-64 h-64">
                <svg viewBox="0 0 200 200" class="w-full h-full">
                    <!-- Outer Hexagon -->
                    <defs>
                        <linearGradient id="grad1" x1="0%" y1="0%" x2="0%" y2="100%">
                            <stop offset="0%" style="stop-color:#8B5CF6;stop-opacity:0.3" />
                            <stop offset="100%" style="stop-color:#7C3AED;stop-opacity:0.8" />
                        </linearGradient>
                    </defs>
                    <polygon points="100,10 175,50 175,130 100,170 25,130 25,50" 
                             fill="url(#grad1)" stroke="#E9D5FF" stroke-width="3"/>
                    
                    <!-- Inner Hexagon -->
                    <polygon points="100,30 160,60 160,120 100,150 40,120 40,60" 
                             fill="#7C3AED" stroke="#E9D5FF" stroke-width="2"/>
                    
                    <!-- Diamond Center -->
                    <polygon points="100,70 120,90 100,110 80,90" 
                             fill="#E9D5FF" stroke="#FFFFFF" stroke-width="2"/>
                </svg>
            </div>
        </div>

        <!-- Choose Your Package -->
        <h2 class="text-xl font-bold text-gray-900 mb-4">Choose Your Package</h2>

        <!-- Current Membership Display -->
        <div class="mb-4 p-4 bg-blue-50 rounded-lg border border-blue-200">
            <p class="text-sm text-gray-600">Current Membership:</p>
            <p class="text-lg font-bold text-blue-600">{{ auth()->user()->membershipTier->name }}</p>
        </div>

        <!-- Custom Dropdown -->
        <div class="relative mb-6" @click.away="dropdownOpen = false">
            <button @click="dropdownOpen = !dropdownOpen" 
                    type="button"
                    class="w-full flex items-center justify-between px-4 py-3 bg-white border-2 border-orange-500 rounded-lg text-left hover:bg-gray-50 transition">
                <span class="font-semibold text-gray-900" x-text="selectedTier.name"></span>
                <svg class="w-5 h-5 text-gray-600 transition-transform" 
                     :class="dropdownOpen ? 'rotate-180' : ''"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>

            <!-- Dropdown Menu -->
            <div x-show="dropdownOpen" 
                 x-cloak
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 transform scale-95"
                 x-transition:enter-end="opacity-100 transform scale-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 transform scale-100"
                 x-transition:leave-end="opacity-0 transform scale-95"
                 class="absolute z-10 w-full mt-2 bg-white border border-gray-200 rounded-lg shadow-xl max-h-64 overflow-y-auto">
                <template x-for="tier in tiers" :key="tier.id">
                    <button @click="selectTier(tier)" 
                            type="button"
                            class="w-full px-4 py-3 text-left hover:bg-gray-50 transition border-b border-gray-100 last:border-b-0"
                            :class="selectedTier.id === tier.id ? 'bg-orange-50' : ''">
                        <div class="flex items-center justify-between">
                            <span class="font-semibold text-gray-900" x-text="tier.name"></span>
                            <span class="text-xs text-gray-500" x-text="'Level ' + tier.level"></span>
                        </div>
                    </button>
                </template>
            </div>
        </div>

        <!-- Benefits List -->
        <div class="space-y-3 mb-6">
            <template x-for="(benefit, index) in selectedTier.benefits" :key="index">
                <div class="flex items-start space-x-3">
                    <div class="w-6 h-6 rounded-full bg-gray-800 flex-shrink-0 mt-0.5"></div>
                    <p class="text-sm text-gray-700" x-text="benefit"></p>
                </div>
            </template>
        </div>

        <!-- Current/Upgrade Button -->
        <template x-if="selectedTier.id === currentTierId">
            <button disabled
                    class="w-full py-3 px-6 bg-gray-400 text-white rounded-lg font-bold text-sm cursor-not-allowed">
                CURRENT
            </button>
        </template>

        <template x-if="selectedTier.id !== currentTierId && selectedTier.level <= currentTierLevel">
            <button disabled
                    class="w-full py-3 px-6 bg-gray-400 text-white rounded-lg font-bold text-sm cursor-not-allowed">
                CANNOT DOWNGRADE
            </button>
        </template>

        <template x-if="selectedTier.id !== currentTierId && selectedTier.level > currentTierLevel">
            <button @click="showUpgradeConfirm = true"
                    class="w-full bg-gradient-to-r from-orange-500 to-red-500 text-white py-3 px-6 rounded-lg font-bold text-sm shadow-lg hover:shadow-xl transition">
                UPGRADE TO <span x-text="selectedTier.name.toUpperCase()"></span>
            </button>
        </template>

        <!-- Upgrade Cost Info -->
        <template x-if="selectedTier.id !== currentTierId && selectedTier.level > currentTierLevel">
            <div class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                <p class="text-sm text-yellow-800 text-center">
                    <i class="fas fa-info-circle mr-2"></i>
                    Upgrade Cost: <span class="font-bold" x-text="selectedTier.upgrade_cost"></span> USD
                </p>
            </div>
        </template>
    </div>

    <!-- Upgrade Confirmation Modal -->
    <div x-show="showUpgradeConfirm" 
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center px-4"
         style="background: rgba(0, 0, 0, 0.6);">
        <div class="bg-white rounded-2xl shadow-2xl max-w-sm w-full p-6 text-center"
             @click.away="showUpgradeConfirm = false"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform scale-90"
             x-transition:enter-end="opacity-100 transform scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform scale-100"
             x-transition:leave-end="opacity-0 transform scale-90">
            
            <h3 class="text-base font-bold text-gray-900 mb-3">Upgrade Membership</h3>
            <p class="text-gray-600 text-sm mb-6">
                Upgrade to <span class="font-bold" x-text="selectedTier.name"></span> for 
                <span class="font-bold text-orange-500" x-text="selectedTier.upgrade_cost"></span> USD?
            </p>
            
            <div class="flex space-x-3">
                <button @click="showUpgradeConfirm = false" 
                        class="flex-1 px-4 py-2.5 border-2 border-gray-300 text-gray-700 rounded-xl font-semibold text-sm hover:bg-gray-50 transition">
                    Cancel
                </button>
                <button @click="showCsPopup = true; showUpgradeConfirm = false"
                        class="flex-1 px-4 py-2.5 bg-gradient-to-r from-orange-500 to-red-500 text-white rounded-xl font-semibold text-sm hover:shadow-lg transition">
                    Confirm
                </button>
            </div>
        </div>
    </div>

    <!-- Customer Service Popup Component -->
    <div x-show="showCsPopup" 
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center px-4 py-4"
         style="background: rgba(0, 0, 0, 0.7);">
        
        <div class="bg-white rounded-3xl shadow-2xl max-w-md w-full overflow-hidden"
             x-transition:enter="transition ease-out duration-300"
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
    function membershipPage() {
        return {
            dropdownOpen: false,
            showUpgradeConfirm: false,
            showCsPopup: false,
            currentTierId: {{ auth()->user()->membership_tier_id }},
            currentTierLevel: {{ auth()->user()->membershipTier->level }},
            tiers: [],
            selectedTier: {},

            async init() {
                await this.fetchTiers();
                // Set current tier as selected by default
                this.selectedTier = this.tiers.find(t => t.id === this.currentTierId) || this.tiers[0];
            },

            async fetchTiers() {
                try {
                    const response = await axios.get('{{ route("membership.tiers") }}');
                    if (response.data.success) {
                        this.tiers = response.data.tiers;
                    }
                } catch (error) {
                    console.error('Error fetching tiers:', error);
                    showAlert('Error loading membership tiers', 'error');
                }
            },

            selectTier(tier) {
                this.selectedTier = tier;
                this.dropdownOpen = false;
            },

            handleUpgradeConfirm() {
                // Close the upgrade confirmation modal first
                this.showUpgradeConfirm = false;
                
                // Wait a moment for the modal to close animation, then open CS popup
                setTimeout(() => {
                    this.showCsPopup = true;
                }, 5000);
            }
        }
    }
</script>
@endpush