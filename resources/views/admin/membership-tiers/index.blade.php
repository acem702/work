@extends('layouts.admin')

@section('title', 'Membership Tiers')

@section('content')
<div class="p-6">
    
    <!-- Page Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Membership Tiers</h1>
            <p class="text-gray-600 mt-1">Manage membership levels and benefits</p>
        </div>
    </div>

    <!-- Tiers Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach(\App\Models\MembershipTier::active()->ordered()->get() as $tier)
            <div class="bg-white rounded-xl shadow-sm border-2 {{ $tier->level === 5 ? 'border-purple-500' : 'border-gray-200' }} overflow-hidden hover:shadow-md transition">
                
                <!-- Tier Header -->
                <div class="bg-gradient-to-br from-primary to-secondary p-6 text-white">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-2xl font-bold">{{ $tier->name }}</h3>
                        <span class="px-3 py-1 bg-white/20 backdrop-blur-sm rounded-full text-sm font-semibold">
                            Level {{ $tier->level }}
                        </span>
                    </div>
                    @if($tier->description)
                        <p class="text-white/90 text-sm">{{ $tier->description }}</p>
                    @endif
                </div>

                <!-- Tier Details -->
                <div class="p-6 space-y-4">
                    
                    <!-- User Count -->
                    <div class="flex items-center justify-between pb-4 border-b border-gray-200">
                        <span class="text-gray-600">Active Users</span>
                        <span class="text-2xl font-bold text-gray-900">
                            {{ \App\Models\User::where('membership_tier_id', $tier->id)->where('status', 'active')->count() }}
                        </span>
                    </div>

                    <!-- Benefits List -->
                    <div class="space-y-3">
                        <!-- Daily Task Limit -->
                        <div class="flex items-center justify-between">
                            <div class="flex items-center text-gray-700">
                                <i class="fas fa-tasks text-blue-500 w-5 mr-3"></i>
                                <span class="text-sm">Daily Task Limit</span>
                            </div>
                            <span class="font-semibold text-gray-900">{{ $tier->daily_task_limit }}</span>
                        </div>

                        <!-- Commission Multiplier -->
                        <div class="flex items-center justify-between">
                            <div class="flex items-center text-gray-700">
                                <i class="fas fa-chart-line text-green-500 w-5 mr-3"></i>
                                <span class="text-sm">Commission Multiplier</span>
                            </div>
                            <span class="font-semibold text-green-600">×{{ $tier->commission_multiplier }}</span>
                        </div>

                        <!-- Upgrade Cost -->
                        <div class="flex items-center justify-between">
                            <div class="flex items-center text-gray-700">
                                <i class="fas fa-coins text-yellow-500 w-5 mr-3"></i>
                                <span class="text-sm">Upgrade Cost</span>
                            </div>
                            <span class="font-semibold text-gray-900">
                                @if($tier->upgrade_cost > 0)
                                    {{ number_format($tier->upgrade_cost) }} pts
                                @else
                                    <span class="text-green-600">Free</span>
                                @endif
                            </span>
                        </div>

                        <!-- Products Available -->
                        <div class="flex items-center justify-between">
                            <div class="flex items-center text-gray-700">
                                <i class="fas fa-box text-purple-500 w-5 mr-3"></i>
                                <span class="text-sm">Products Available</span>
                            </div>
                            <span class="font-semibold text-gray-900">
                                {{ \App\Models\Product::whereHas('minMembershipTier', fn($q) => $q->where('level', '<=', $tier->level))->count() }}
                            </span>
                        </div>
                    </div>

                    <!-- Commission Examples -->
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <p class="text-xs font-semibold text-gray-600 mb-2">Commission Examples:</p>
                        <div class="grid grid-cols-3 gap-2 text-center text-xs">
                            <div class="bg-gray-50 rounded p-2">
                                <p class="text-gray-600">Base: 10</p>
                                <p class="font-bold text-gray-900">{{ 10 * $tier->commission_multiplier }}</p>
                            </div>
                            <div class="bg-gray-50 rounded p-2">
                                <p class="text-gray-600">Base: 50</p>
                                <p class="font-bold text-gray-900">{{ 50 * $tier->commission_multiplier }}</p>
                            </div>
                            <div class="bg-gray-50 rounded p-2">
                                <p class="text-gray-600">Base: 100</p>
                                <p class="font-bold text-gray-900">{{ 100 * $tier->commission_multiplier }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Edit Button -->
                    <div class="pt-4">
                        <a href="#" 
                           class="block w-full px-4 py-2 bg-primary text-white text-center rounded-lg hover:bg-primary/90 transition">
                            <i class="fas fa-edit mr-2"></i>
                            Edit Tier
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

</div>
@endsection