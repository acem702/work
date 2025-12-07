@extends('layouts.user')

@section('title', 'Recharge')

@section('content')
<div class="space-y-6">
    
    <!-- Page Title -->
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900">Recharge</h1>

        <a href="{{ route('dashboard') }}" 
        class="flex items-center space-x-2 px-4 py-2 bg-gradient-to-r from-orange-500 to-red-500 rounded-xl text-white text-sm font-bold shadow-lg hover:shadow-xl transition">
            <i class="fas fa-arrow-left"></i>
            <span>Dashboard</span>
        </a>
    </div>

    <!-- Orange Divider Line -->
    <div class="w-full h-1 bg-gradient-to-r from-orange-500 to-orange-400"></div>

    <!-- Instructions -->
    <div class="bg-white rounded-2xl shadow-lg p-5 border border-gray-100">
        <h2 class="text-lg font-bold text-gray-900 mb-3">How can we help?</h2>
        <p class="text-xs text-gray-600">
            Our dedicated team is available to answer all your questions.
        </p>
        <p class="text-xs text-gray-600">
            Everyday, 10:00 to 22:00.
        </p>
        <p class="text-xs text-gray-600">
            If you get in touch outside of these hours we will aim to respond to you as quickly as possible the next working day.
        </p>
    </div>

    <!-- Recharge Button -->
    <div class="flex justify-center py-4">
        <button @click="showCsPopup = true"
                class="w-full gradient-button max-w-xs text-white py-3 px-6 rounded-xl font-bold text-base shadow-lg transition">
            Recharge Now
        </button>
    </div>
    <div class="bg-white rounded-2xl shadow-lg p-5 border border-gray-100 text-center">
        <p class="text-xs text-gray-600 mb-3">
            Recharge remark:
        </p>
        <p class="text-xs text-gray-600">
            1. Receiving account: Account Funds​
        </p>
        <p class="text-xs text-gray-600">
            2. The deposit will be credited and available for trading once you receive confirmation from customer support.​
        </p>
        <p class="text-xs text-gray-600">
            3. Please make sure your selected coins and network are correct before sending any funds to the deposit address provided by customer support. Sending funds over an incorrect network or in different coins will result in the loss of your assets, which cannot be retrieved.​
        </p>
        <p class="text-xs text-gray-600">
            4. Please contact our customer service to request the latest deposit address.
        </p>
    </div>
    @include('partials.customer-service-popup')
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