@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="p-6">
    
    <!-- Page Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
        <p class="text-gray-600 mt-1">Welcome back, {{ auth()->user()->name }}!</p>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        
        <!-- Total Users -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Users</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['total_users'] }}</p>
                    <p class="text-sm text-gray-500 mt-1">
                        <span class="text-green-600 font-semibold">{{ $stats['active_users'] }}</span> active
                    </p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-users text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Products -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Products</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['total_products'] }}</p>
                    <p class="text-sm text-gray-500 mt-1">
                        <span class="text-green-600 font-semibold">{{ $stats['active_products'] }}</span> active
                    </p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-box text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Completed Tasks -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Completed Tasks</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['total_tasks_completed'] }}</p>
                    <p class="text-sm text-gray-500 mt-1">
                        <span class="text-yellow-600 font-semibold">{{ $stats['total_tasks_pending'] }}</span> pending
                    </p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Points Distributed -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Points Distributed</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($stats['total_points_distributed']) }}</p>
                    <p class="text-sm text-gray-500 mt-1">
                        <span class="text-blue-600 font-semibold">{{ number_format($stats['total_referral_earnings']) }}</span> referrals
                    </p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-coins text-yellow-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        
        <!-- Daily Statistics Chart -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Tasks Last 7 Days</h2>
            <div class="h-64">
                <canvas id="dailyStatsChart"></canvas>
            </div>
        </div>

        <!-- Membership Distribution -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Membership Distribution</h2>
            <div class="space-y-4">
                @foreach($membershipDistribution as $dist)
                    @php
                        $percentage = $stats['total_users'] > 0 ? ($dist->count / $stats['total_users']) * 100 : 0;
                    @endphp
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">{{ $dist->membershipTier->name }}</span>
                            <span class="text-sm font-semibold text-gray-900">{{ $dist->count }} ({{ number_format($percentage, 1) }}%)</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-gradient-to-r from-primary to-secondary h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Top Performers -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900">Top Performers</h2>
                <a href="{{ route('admin.users.index') }}" class="text-sm text-primary hover:text-primary/80">View All</a>
            </div>
            <div class="space-y-4">
                @forelse($topUsers as $index => $user)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 rounded-full bg-primary flex items-center justify-center text-white font-semibold text-sm">
                                {{ $index + 1 }}
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $user->name }}</p>
                                <p class="text-xs text-gray-500">{{ $user->completed_tasks }} tasks</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-semibold text-green-600">+{{ number_format($user->total_earned) }}</p>
                            <p class="text-xs text-gray-500">points</p>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-4">No data available</p>
                @endforelse
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900">Recent Tasks</h2>
            </div>
            <div class="space-y-3">
                @forelse($recentTasks as $task)
                    <div class="flex items-center justify-between p-3 border-l-4 {{ $task->status === 'completed' ? 'border-green-500 bg-green-50' : 'border-yellow-500 bg-yellow-50' }}">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">{{ $task->user->name }}</p>
                            <p class="text-xs text-gray-600">{{ $task->product->name }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ $task->created_at->diffForHumans() }}</p>
                        </div>
                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ status_badge_color($task->status) }}">
                            {{ ucfirst($task->status) }}
                        </span>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-4">No recent tasks</p>
                @endforelse
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Daily Stats Chart
    const ctx = document.getElementById('dailyStatsChart').getContext('2d');
    const dailyData = @json($dailyStats);
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: dailyData.map(d => new Date(d.date).toLocaleDateString('en-US', { month: 'short', day: 'numeric' })),
            datasets: [{
                label: 'Tasks Completed',
                data: dailyData.map(d => d.count),
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            }
        }
    });
</script>
@endpush