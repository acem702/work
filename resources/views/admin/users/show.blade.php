@extends('layouts.admin')

@section('title', 'User Details - ' . $user->name)

@section('content')
<div class="p-6">
    
    <!-- Back Button -->
    <div class="mb-6">
        <a href="{{ route('admin.users.index') }}" class="text-primary hover:text-primary/80 inline-flex items-center">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to Users
        </a>
    </div>

    <!-- User Profile Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex items-start justify-between">
            <div class="flex items-center space-x-4">
                <div class="w-20 h-20 rounded-full bg-primary flex items-center justify-center text-white font-bold text-2xl">
                    {{ substr($user->name, 0, 1) }}
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $user->name }}</h1>
                    <p class="text-gray-600">{{ $user->email }}</p>
                    <div class="flex items-center space-x-2 mt-2">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $user->role === 'agent' ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ ucfirst($user->role) }}
                        </span>
                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ membership_badge_color($user->membershipTier->level) }}">
                            {{ $user->membershipTier->name }}
                        </span>
                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ status_badge_color($user->status) }}">
                            {{ ucfirst($user->status) }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="text-right">
                <p class="text-sm text-gray-600">Referral Code</p>
                <p class="text-xl font-bold text-primary">{{ $user->referral_code }}</p>
            </div>
        </div>

        @if($user->referrer)
            <div class="mt-4 pt-4 border-t border-gray-200">
                <p class="text-sm text-gray-600">
                    Referred by: <a href="{{ route('admin.users.show', $user->referrer) }}" class="text-primary hover:underline font-semibold">{{ $user->referrer->name }}</a>
                </p>
            </div>
        @endif
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Point Balance</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($user->point_balance, 2) }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-coins text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Tasks</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['total_tasks'] }}</p>
                    <p class="text-sm text-gray-500 mt-1">
                        <span class="text-yellow-600 font-semibold">{{ $stats['pending_tasks'] }}</span> pending
                    </p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-tasks text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Earned</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($stats['total_earned'], 2) }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-chart-line text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Referrals</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['total_referrals'] }}</p>
                    <p class="text-sm text-gray-500 mt-1">
                        <span class="text-green-600 font-semibold">{{ number_format($stats['referral_earnings'], 2) }}</span> earned
                    </p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-users text-yellow-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Task Queue -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900">Task Queue</h2>
                <a href="{{ route('admin.task-queue.user.queue', $user) }}" class="text-sm text-primary hover:text-primary/80">View All</a>
            </div>
            <div class="space-y-3">
                @forelse($user->taskQueues->take(5) as $taskQueue)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">{{ $taskQueue->product->name }}</p>
                            <p class="text-xs text-gray-500 mt-1">
                                Points: {{ number_format($taskQueue->product->base_points) }} | 
                                Commission: {{ number_format($taskQueue->product->calculateCommission($user)) }}
                            </p>
                        </div>
                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ status_badge_color($taskQueue->status) }}">
                            {{ ucfirst($taskQueue->status) }}
                        </span>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-4">No tasks in queue</p>
                @endforelse
            </div>
        </div>

        <!-- Recent Tasks -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Recent Tasks</h2>
            <div class="space-y-3">
                @forelse($user->tasks->take(5) as $task)
                    <div class="flex items-center justify-between p-3 border-l-4 {{ $task->status === 'completed' ? 'border-green-500 bg-green-50' : 'border-yellow-500 bg-yellow-50' }}">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">{{ $task->product->name }}</p>
                            <p class="text-xs text-gray-600">
                                Commission: {{ number_format($task->commission_earned) }} points
                            </p>
                            <p class="text-xs text-gray-500 mt-1">{{ $task->created_at->diffForHumans() }}</p>
                        </div>
                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ status_badge_color($task->status) }}">
                            {{ ucfirst($task->status) }}
                        </span>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-4">No tasks completed</p>
                @endforelse
            </div>
        </div>

        <!-- Recent Transactions -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 lg:col-span-2">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Recent Transactions</h2>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Balance</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($user->transactions->take(10) as $transaction)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3">
                                    <span class="text-xs font-medium text-gray-700">
                                        {{ transaction_type_label($transaction->type) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="text-sm font-semibold {{ $transaction->amount >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $transaction->amount >= 0 ? '+' : '' }}{{ number_format($transaction->amount, 2) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900">
                                    {{ number_format($transaction->balance_after, 2) }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600">
                                    {{ $transaction->description }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-500">
                                    {{ $transaction->created_at->format('M d, H:i') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                    No transactions yet
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Referrals -->
        @if($user->referrals->count() > 0)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 lg:col-span-2">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Referrals ({{ $user->referrals->count() }})</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($user->referrals as $referral)
                        <a href="{{ route('admin.users.show', $referral) }}" 
                           class="p-4 border border-gray-200 rounded-lg hover:border-primary hover:shadow-sm transition">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 rounded-full bg-primary flex items-center justify-center text-white font-semibold">
                                    {{ substr($referral->name, 0, 1) }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $referral->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $referral->membershipTier->name }}</p>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

</div>
@endsection