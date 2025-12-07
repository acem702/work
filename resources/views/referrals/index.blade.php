@extends('layouts.user')

@section('title', 'Referral code')

@section('content')
<div x-data="referralPage()" class="space-y-6">
    
    <!-- Page Title -->
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900">Referral code</h1>

        <a href="{{ route('dashboard') }}" 
        class="flex items-center space-x-2 px-4 py-2 bg-gradient-to-r from-orange-500 to-red-500 rounded-xl text-white text-sm font-bold shadow-lg hover:shadow-xl transition">
            <i class="fas fa-arrow-left"></i>
            <span>Dashboard</span>
        </a>
    </div>

    <!-- Orange Divider Line -->
    <div class="w-full h-1 bg-gradient-to-r from-orange-500 to-orange-400"></div>

    <!-- Main Content Card -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
        
        <!-- Heading -->
        <h2 class="text-xl font-bold text-gray-900 mb-4">
            Unlock Incredible Benefits with Our Referral Program!
        </h2>

        <!-- Divider -->
        <div class="w-full h-px bg-gray-300 mb-6"></div>

        <!-- Description -->
        <div class="space-y-4 mb-8 text-sm text-gray-700 leading-relaxed">
            <p>
                Do you know someone talented and motivated who would be a perfect fit for our company? Refer them to us and unlock incredible benefits! Not only will you help a friend discover an exciting career opportunity, but you'll also receive fantastic rewards as a token of our appreciation.
            </p>

            <div class="space-y-2">
                <p class="font-semibold">Terms and Conditions:</p>
                <p>1. You must be a Silver member or higher to participate.</p>
                <p>2. You must have completed at least one full cycle of the basic salary.</p>
            </div>

            <p>
                Take advantage of this opportunity to benefit both your friends and your career!
            </p>
        </div>

        <!-- Referral Code Section -->
        <div class="bg-gray-50 rounded-xl p-6">
            <div class="mb-4">
                <p class="text-sm text-gray-600 mb-2">Referral Code</p>
                <p class="text-4xl font-bold text-gray-900 tracking-wider" x-text="referralCode"></p>
            </div>
            
            <button @click="copyReferralCode" 
                    class="w-full gradient-button text-white py-3 px-6 rounded-lg font-bold text-sm shadow-lg hover:shadow-xl transition">
                Copy Referral Code
            </button>
        </div>
    </div>

    <!-- Referral Statistics (Optional) -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Your Referral Stats</h3>
        
        <div class="grid grid-cols-2 gap-4">
            <div class="bg-blue-50 rounded-xl p-4 text-center">
                <p class="text-2xl font-bold text-blue-600" x-text="stats.total_referrals"></p>
                <p class="text-xs text-gray-600 mt-1">Total Referrals</p>
            </div>
            <div class="bg-green-50 rounded-xl p-4 text-center">
                <p class="text-2xl font-bold text-green-600" x-text="stats.total_earnings"></p>
                <p class="text-xs text-gray-600 mt-1">Total Earnings (USD)</p>
            </div>
        </div>
    </div>

    <!-- Referrals List -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Your Referrals</h3>
        
        <div class="space-y-3">
            <template x-for="referral in referrals" :key="referral.id">
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-primary rounded-full flex items-center justify-center text-white font-bold">
                            <span x-text="referral.initial"></span>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-900" x-text="referral.name"></p>
                            <p class="text-xs text-gray-500" x-text="referral.membership"></p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-bold text-green-600" x-text="referral.earnings"></p>
                        <p class="text-xs text-gray-500">Earned</p>
                    </div>
                </div>
            </template>

            <template x-if="referrals.length === 0">
                <div class="py-8 text-center">
                    <p class="text-gray-400 text-sm">No referrals yet. Start sharing your code!</p>
                </div>
            </template>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    function referralPage() {
        return {
            referralCode: '{{ auth()->user()->referral_code }}',
            stats: {
                total_referrals: 0,
                total_earnings: '0.00'
            },
            referrals: [],

            init() {
                this.fetchReferralData();
            },

            copyReferralCode() {
                navigator.clipboard.writeText(this.referralCode).then(() => {
                    showAlert('Referral code copied to clipboard!', 'success');
                }).catch(() => {
                    showAlert('Failed to copy referral code', 'error');
                });
            },

            async fetchReferralData() {
                try {
                    const response = await axios.get('{{ route("referrals.data") }}');
                    
                    if (response.data.success) {
                        this.stats = response.data.stats;
                        this.referrals = response.data.referrals;
                    }
                } catch (error) {
                    console.error('Error fetching referral data:', error);
                }
            }
        }
    }
</script>
@endpush