@extends('layouts.user')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">
    
    <!-- Page Title -->
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
    </div>

    <!-- Orange Divider Line -->
    <div class="w-full h-1 bg-gradient-to-r from-orange-500 to-orange-400"></div>

    <!-- Welcome Card -->
    <div class="bg-white rounded-2xl shadow-lg p-5 border border-gray-100">
        <h2 class="text-xl font-bold text-gray-900 mb-1">Welcome,</h2>
        <h3 class="text-xl font-bold text-gray-900 mb-4">{{ auth()->user()->name }}</h3>
        
        <div class="border-t border-gray-300 pt-4 space-y-2">
            <p class="text-sm">
                <span class="text-orange-500 font-medium">Membership Tier:</span>
                <span class="text-gray-900 font-semibold"> {{ auth()->user()->membershipTier->name }}</span>
            </p>
            <p class="text-sm">
                <span class="text-orange-500 font-medium">Credibility :</span>
                <span class="text-gray-900 font-semibold">100</span>
            </p>
        </div>
    </div>

    <!-- Account Balance Section -->
    <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 text-center">
        <p class="text-4xl font-bold text-gray-900 mb-2">{{ number_format(auth()->user()->point_balance, 2) }}</p>
        <p class="text-sm text-gray-600">Account Balance (USD)</p>
    </div>

    <!-- Frozen Amount -->
    <div class="border-t-2 border-orange-500 pt-4">
        <div class="text-center">
            <p class="text-4xl font-bold text-gray-900 mb-2">{{ number_format(auth()->user()->tasks()->where('status','pending')->sum('points_locked')) }}</p>
            <p class="text-sm text-gray-600">Frozen Amount (USD)</p>
        </div>
    </div>

    <!-- Today's Commission -->
    <div class="border-t-2 border-orange-500 pt-4">
        <div class="text-center">
            <p class="text-4xl font-bold text-gray-900 mb-2">{{ number_format(auth()->user()->tasks()->whereDate('completed_at', today())->sum('commission_earned'), 2) }}</p>
            <p class="text-sm text-gray-600">Today's Commission (USD)</p>
        </div>
    </div>

    <!-- Registered Working Days Section -->
    <div class="bg-gray-50 rounded-2xl p-5">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-base font-bold text-orange-500">Registered working days</h2>
            <button class="px-4 py-2 bg-gray-400 text-white rounded-full text-xs font-medium">
                Sign in immediately ({{ auth()->user()->tasks_completed_today }}/{{ auth()->user()->membershipTier->daily_task_limit }})
            </button>
        </div>

        <!-- Days Cards -->
        <div class="flex overflow-x-auto space-x-3 pb-2">
            @for($i = 1; $i <= 7; $i++)
                <div class="flex-shrink-0 w-40 bg-white rounded-xl border-2 border-orange-500 overflow-hidden">
                    <div class="bg-white p-4 flex items-center justify-center">
                        <svg class="w-12 h-12 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="bg-orange-500 py-2 text-center flex items-center justify-center space-x-2">
                        <i class="fas fa-lock text-white text-xs"></i>
                        <span class="text-white text-xs font-bold">DAY {{ $i }}</span>
                    </div>
                </div>
            @endfor
        </div>
    </div>

    <!-- Statistics Card (Alternative View) -->
    <div class="bg-gradient-to-br from-blue-900 via-blue-800 to-green-600 rounded-2xl shadow-xl p-6 text-center text-white">
        <div class="space-y-6">
            <!-- Today's Commission -->
            <div>
                <p class="text-4xl font-bold mb-2">{{ number_format(auth()->user()->tasks()->whereDate('completed_at', today())->sum('commission_earned'), 2) }}</p>
                <p class="text-sm text-gray-200">Today's Commission(USD)</p>
            </div>

            <div class="border-t border-white border-opacity-30"></div>

            <!-- Account Balance -->
            <div>
                <p class="text-4xl font-bold mb-2">{{ number_format(auth()->user()->point_balance, 2) }}</p>
                <p class="text-sm text-gray-200">Account Balance (USD)</p>
            </div>

            <div class="border-t border-white border-opacity-30"></div>

            <!-- Data (Tasks) -->
            <div>
                <p class="text-4xl font-bold mb-2">{{ auth()->user()->tasks_completed_today }} / {{ auth()->user()->membershipTier->daily_task_limit }}</p>
                <p class="text-sm text-gray-200">Data</p>
            </div>

            <div class="border-t border-white border-opacity-30"></div>

            <!-- Frozen Amount -->
            <div>
                <p class="text-4xl font-bold mb-2">0</p>
                <p class="text-sm text-gray-200">Frozen Amount (USD)</p>
            </div>
        </div>
    </div>

    <!-- Asset Section -->
    <div class="mt-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-4">Asset</h2>

        <!-- Access Card -->
        <div onclick="window.location='{{ route('tasks.index') }}'" class="bg-white rounded-2xl shadow-lg p-6 mb-4 text-center border border-gray-100">
            <div class="w-16 h-16 bg-blue-500 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-user-plus text-white text-2xl"></i>
            </div>
            <h3 class="text-base font-bold text-gray-900 mb-2">Access</h3>
            <p class="text-xs text-gray-500 mb-4">Explore your loyalty rewards & track your progress</p>
            <a href="{{ route('tasks.index') }}" class="text-orange-500 font-semibold text-sm hover:text-orange-600">Explore More ></a>
        </div>

        <!-- Recharge Card -->
        <div onclick="window.location='{{ route(name: 'recharge') }}'" class="bg-white rounded-2xl shadow-lg p-6 mb-4 text-center border border-gray-100">
            <div class="w-16 h-16 bg-blue-500 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-shopping-cart text-white text-2xl"></i>
            </div>
            <h3 class="text-base font-bold text-gray-900 mb-2">Recharge</h3>
            <p class="text-xs text-gray-500 mb-4">Recharge to increase your profits</p>
            <a href="{{ route(name: 'recharge') }}" class="text-orange-500 font-semibold text-sm hover:text-orange-600">Explore More ></a>
        </div>

        <!-- Withdrawal Card -->
        <div onclick="window.location='{{ route(name: 'withdrawals.index') }}'" class="bg-white rounded-2xl shadow-lg p-6 mb-4 text-center border border-gray-100">
            <div class="w-16 h-16 bg-blue-500 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-wallet text-white text-2xl"></i>
            </div>
            <h3 class="text-base font-bold text-gray-900 mb-2">Withdrawal</h3>
            <p class="text-xs text-gray-500 mb-4">Cash out your funds</p>
            <a href="{{ route(name: 'withdrawals.index') }}" class="text-orange-500 font-semibold text-sm hover:text-orange-600">Explore More ></a>
        </div>
    </div>

    <!-- Profile Section -->
    <div class="mt-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-4">Profile</h2>

        <!-- Transaction History Card -->
        <div onclick="window.location='{{ route('transactions.index') }}'" class="bg-white rounded-2xl shadow-lg p-6 mb-4 text-center border border-gray-100">
            <div class="w-16 h-16 bg-blue-500 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-file-invoice text-white text-2xl"></i>
            </div>
            <h3 class="text-base font-bold text-gray-900 mb-2">Transaction History</h3>
            <p class="text-xs text-gray-500 mb-4">Track your recharges, withdrawals & earnings history</p>
            <a href="{{ route('transactions.index') }}" class="text-orange-500 font-semibold text-sm hover:text-orange-600">Explore More ></a>
        </div>

        <!-- My Account Card -->
        <div onclick="window.location='{{ route(name: 'account.index') }}'" class="bg-white rounded-2xl shadow-lg p-6 mb-4 text-center border border-gray-100">
            <div class="w-16 h-16 bg-blue-500 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-key text-white text-2xl"></i>
            </div>
            <h3 class="text-base font-bold text-gray-900 mb-2">My Account</h3>
            <p class="text-xs text-gray-500 mb-4">Manage your sign in & password details</p>
            <a href="{{ route(name: 'account.index') }}" class="text-orange-500 font-semibold text-sm hover:text-orange-600">Explore More ></a>
        </div>

        <!-- Referral Code Card -->
        <div onclick="window.location='{{ route('referrals.index') }}'" class="bg-white rounded-2xl shadow-lg p-6 mb-4 text-center border border-gray-100">
            <div class="w-16 h-16 bg-blue-500 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-users text-white text-2xl"></i>
            </div>
            <h3 class="text-base font-bold text-gray-900 mb-2">Referral Code</h3>
            <p class="text-xs text-gray-500 mb-4">Get your amazing rewards</p>
            <a href="{{ route('referrals.index') }}" class="text-orange-500 font-semibold text-sm hover:text-orange-600">Explore More ></a>
        </div>
    </div>

    <!-- History Section -->
    <div class="mt-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-4">History</h2>

        <!-- Orders Card -->
        <div onclick="window.location='{{ route('tasks.orders') }}'" class="bg-white rounded-2xl shadow-lg p-6 mb-4 text-center border border-gray-100">
            <div class="w-16 h-16 bg-blue-500 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-box text-white text-2xl"></i>
            </div>
            <h3 class="text-base font-bold text-gray-900 mb-2">Orders</h3>
            <p class="text-xs text-gray-500 mb-4">Track your orders status</p>
            <a href="{{ route('tasks.orders') }}" class="text-orange-500 font-semibold text-sm hover:text-orange-600">Explore More ></a>
        </div>

        <!-- Funds Card -->
        <div onclick="window.location='{{ route('transactions.index') }}'" class="bg-white rounded-2xl shadow-lg p-6 mb-4 text-center border border-gray-100">
            <div class="w-16 h-16 bg-blue-500 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-file-invoice-dollar text-white text-2xl"></i>
            </div>
            <h3 class="text-base font-bold text-gray-900 mb-2">Funds</h3>
            <p class="text-xs text-gray-500 mb-4">Track your recharges, withdrawals & earnings history</p>
            <a href="{{ route('transactions.index') }}" class="text-orange-500 font-semibold text-sm hover:text-orange-600">Explore More ></a>
        </div>

        <!-- Bind Wallet Address Card -->
        <div onclick="window.location='{{ route(name: 'withdrawal-method.index') }}'" class="bg-white rounded-2xl shadow-lg p-6 mb-4 text-center border border-gray-100">
            <div class="w-16 h-16 bg-blue-500 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-link text-white text-2xl"></i>
            </div>
            <h3 class="text-base font-bold text-gray-900 mb-2">Bind Wallet Address</h3>
            <p class="text-xs text-gray-500 mb-4">Bind your wallet information</p>
            <a href="{{ route(name: 'withdrawal-method.index') }}" class="text-orange-500 font-semibold text-sm hover:text-orange-600">Explore More ></a>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    // Add any interactive functionality here
</script>
@endpush