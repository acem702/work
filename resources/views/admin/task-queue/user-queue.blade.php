@extends('layouts.admin')

@section('title', 'Task Queue - ' . $user->name)

@section('content')
<div class="p-6">
    
    <!-- Back Button -->
    <div class="mb-6">
        <a href="{{ route('admin.task-queue.index') }}" class="text-primary hover:text-primary/80 inline-flex items-center">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to Task Assignment
        </a>
    </div>

    <!-- User Info Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="w-16 h-16 rounded-full bg-primary flex items-center justify-center text-white font-bold text-xl">
                    {{ substr($user->name, 0, 1) }}
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $user->name }}</h1>
                    <p class="text-gray-600">{{ $user->email }}</p>
                    <div class="flex items-center space-x-2 mt-2">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ membership_badge_color($user->membershipTier->level) }}">
                            {{ $user->membershipTier->name }}
                        </span>
                        <span class="text-sm text-gray-600">
                            Balance: <span class="font-bold text-gray-900">{{ number_format($user->point_balance) }}</span> points
                        </span>
                    </div>
                </div>
            </div>
            <a href="{{ route('admin.users.show', $user) }}" 
               class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
                <i class="fas fa-eye mr-2"></i>
                View Profile
            </a>
        </div>
    </div>

    <!-- Queue Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Queued Tasks</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $user->taskQueues()->queued()->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clock text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Active Tasks</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $user->taskQueues()->active()->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-hourglass-half text-yellow-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Completed</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $user->taskQueues()->completed()->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Tasks</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $user->taskQueues()->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-tasks text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Replace the Task Queue List section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Task Queue</h2>
        </div>

        <div class="divide-y divide-gray-200">
            @forelse($user->taskQueues()->ordered()->get() as $taskQueue)
                @if($taskQueue->is_combo)
                    <!-- COMBO TASK DISPLAY -->
                    <div class="p-6 bg-gradient-to-r from-purple-50 to-white hover:from-purple-100 hover:to-purple-50 transition">
                        <div class="flex items-start justify-between">
                            <div class="flex items-start space-x-4 flex-1">
                                <!-- Order Number -->
                                <div class="w-12 h-12 rounded-full {{ 
                                    $taskQueue->status === 'completed' ? 'bg-green-100 text-green-600' : 
                                    ($taskQueue->status === 'active' ? 'bg-yellow-100 text-yellow-600' : 'bg-purple-100 text-purple-600') 
                                }} flex items-center justify-center font-bold text-lg flex-shrink-0">
                                    {{ $taskQueue->sequence_order }}
                                </div>

                                <!-- Combo Info -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start justify-between mb-3">
                                        <div>
                                            <div class="flex items-center gap-2 mb-2">
                                                <i class="fas fa-layer-group text-purple-600"></i>
                                                <h3 class="text-lg font-semibold text-gray-900">{{ $taskQueue->comboTask->name }}</h3>
                                                <span class="px-2 py-1 bg-purple-600 text-white text-xs font-bold rounded-full">
                                                    COMBO: {{ $taskQueue->comboTask->sequence_count }} TASKS
                                                </span>
                                            </div>
                                            @if($taskQueue->comboTask->description)
                                                <p class="text-sm text-gray-600 mt-1">{{ $taskQueue->comboTask->description }}</p>
                                            @endif
                                        </div>
                                        <span class="ml-4 px-3 py-1 text-sm font-semibold rounded-full {{ status_badge_color($taskQueue->status) }}">
                                            {{ ucfirst($taskQueue->status) }}
                                        </span>
                                    </div>

                                    <!-- Combo Task Sequence -->
                                    <div class="bg-white rounded-lg border-2 border-purple-200 p-4 mb-4">
                                        <h4 class="text-xs font-semibold text-purple-700 mb-3 uppercase">Task Sequence:</h4>
                                        <div class="space-y-2">
                                            @foreach($taskQueue->comboTask->items as $item)
                                                @php
                                                    // Check if this step has been completed
                                                    $stepTask = $user->tasks()
                                                        ->where('combo_task_id', $taskQueue->comboTask->id)
                                                        ->where('combo_sequence', $item->sequence_order)
                                                        ->first();
                                                    
                                                    $stepStatus = $stepTask ? $stepTask->status : 'not_started';
                                                @endphp
                                                <div class="flex items-center gap-3 p-3 bg-purple-50 rounded-lg border border-purple-200">
                                                    <!-- Step Number -->
                                                    <div class="w-8 h-8 rounded-full bg-purple-600 text-white flex items-center justify-center text-sm font-bold flex-shrink-0">
                                                        {{ $item->sequence_order }}
                                                    </div>

                                                    <!-- Product Info -->
                                                    <div class="flex-1">
                                                        <p class="text-sm font-semibold text-gray-900">{{ $item->product->name }}</p>
                                                        <div class="flex items-center gap-4 text-xs text-gray-600 mt-1">
                                                            <span>
                                                                <i class="fas fa-coins text-purple-600"></i>
                                                                {{ number_format($item->product->base_points) }}
                                                            </span>
                                                            <span>
                                                                <i class="fas fa-gift text-green-600"></i>
                                                                {{ number_format($item->product->base_commission) }}
                                                            </span>
                                                        </div>
                                                    </div>

                                                    <!-- Step Status -->
                                                    @if($stepStatus === 'completed')
                                                        <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded-full">
                                                            <i class="fas fa-check mr-1"></i>Completed
                                                        </span>
                                                    @elseif($stepStatus === 'pending')
                                                        <span class="px-2 py-1 bg-yellow-100 text-yellow-700 text-xs font-semibold rounded-full">
                                                            <i class="fas fa-hourglass-half mr-1"></i>Pending
                                                        </span>
                                                    @else
                                                        <span class="px-2 py-1 bg-gray-100 text-gray-600 text-xs font-semibold rounded-full">
                                                            <i class="fas fa-lock mr-1"></i>Locked
                                                        </span>
                                                    @endif
                                                </div>

                                                @if(!$loop->last)
                                                    <div class="flex justify-center py-1">
                                                        <i class="fas fa-arrow-down text-purple-400"></i>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>

                                    <!-- Combo Summary Stats -->
                                    <div class="grid grid-cols-3 gap-4">
                                        <div class="bg-blue-50 rounded-lg p-3 border border-blue-200">
                                            <p class="text-xs text-gray-600">Total Points</p>
                                            <p class="text-lg font-bold text-blue-600">{{ number_format($taskQueue->comboTask->total_base_points) }}</p>
                                        </div>
                                        <div class="bg-green-50 rounded-lg p-3 border border-green-200">
                                            <p class="text-xs text-gray-600">Total Commission</p>
                                            <p class="text-lg font-bold text-green-600">
                                                {{ number_format($taskQueue->comboTask->items->sum(fn($i) => $i->product->base_commission)) }}
                                            </p>
                                        </div>
                                        <div class="bg-purple-50 rounded-lg p-3 border border-purple-200">
                                            <p class="text-xs text-gray-600">Progress</p>
                                            @php
                                                $completedSteps = $user->tasks()
                                                    ->where('combo_task_id', $taskQueue->comboTask->id)
                                                    ->where('status', 'completed')
                                                    ->count();
                                            @endphp
                                            <p class="text-lg font-bold text-purple-600">
                                                {{ $completedSteps }}/{{ $taskQueue->comboTask->sequence_count }}
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Timeline -->
                                    <div class="flex items-center space-x-6 mt-4 text-sm text-gray-600">
                                        <div class="flex items-center">
                                            <i class="fas fa-calendar-plus text-gray-400 mr-2"></i>
                                            <span>Assigned: {{ $taskQueue->assigned_at->format('M d, Y H:i') }}</span>
                                        </div>
                                        @if($taskQueue->activated_at)
                                            <div class="flex items-center">
                                                <i class="fas fa-play text-gray-400 mr-2"></i>
                                                <span>Started: {{ $taskQueue->activated_at->format('M d, Y H:i') }}</span>
                                            </div>
                                        @endif
                                        @if($taskQueue->completed_at)
                                            <div class="flex items-center">
                                                <i class="fas fa-check text-gray-400 mr-2"></i>
                                                <span>Completed: {{ $taskQueue->completed_at->format('M d, Y H:i') }}</span>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Check for pending task with deficit -->
                                    @php
                                        $pendingComboTask = $user->tasks()
                                            ->where('combo_task_id', $taskQueue->comboTask->id)
                                            ->where('status', 'pending')
                                            ->first();
                                    @endphp
                                    
                                    @if($pendingComboTask && $user->point_balance < 0)
                                        <div class="mt-3 bg-red-50 border-2 border-red-200 rounded-lg p-4">
                                            <div class="flex items-start gap-3">
                                                <i class="fas fa-exclamation-triangle text-red-600 text-xl mt-0.5"></i>
                                                <div class="flex-1">
                                                    <p class="text-sm font-semibold text-red-800">⚠️ DEFICIT DETECTED - Top-up Required</p>
                                                    <p class="text-sm text-red-700 mt-1">
                                                        User has a pending combo task (Step {{ $pendingComboTask->combo_sequence }}) with insufficient balance.
                                                    </p>
                                                    <div class="mt-2 p-2 bg-white rounded border border-red-200">
                                                        <p class="text-xs text-gray-700">
                                                            <strong>Current Balance:</strong> 
                                                            <span class="text-red-600 font-bold">{{ number_format($user->point_balance, 2) }}</span>
                                                        </p>
                                                        <p class="text-xs text-gray-700 mt-1">
                                                            <strong>Deficit Amount:</strong> 
                                                            <span class="text-red-600 font-bold">{{ number_format(abs($user->point_balance), 2) }}</span>
                                                        </p>
                                                    </div>
                                                    <a href="{{ route('admin.users.show', $user) }}" 
                                                    class="inline-flex items-center mt-2 px-3 py-1 bg-red-600 text-white text-xs font-semibold rounded hover:bg-red-700 transition">
                                                        <i class="fas fa-plus mr-1"></i>
                                                        Top Up User Balance
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Actions -->
                            @if($taskQueue->status === 'queued')
                                <button onclick="removeFromQueue({{ $taskQueue->id }})" 
                                        class="ml-4 px-3 py-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition text-sm">
                                    <i class="fas fa-times mr-1"></i> Remove
                                </button>
                            @endif
                        </div>
                    </div>

                @else
                    <!-- REGULAR TASK DISPLAY -->
                    <div class="p-6 hover:bg-gray-50 transition">
                        <div class="flex items-start justify-between">
                            <div class="flex items-start space-x-4 flex-1">
                                <!-- Order Number -->
                                <div class="w-12 h-12 rounded-full {{ 
                                    $taskQueue->status === 'completed' ? 'bg-green-100 text-green-600' : 
                                    ($taskQueue->status === 'active' ? 'bg-yellow-100 text-yellow-600' : 'bg-purple-100 text-purple-600') 
                                }} flex items-center justify-center font-bold text-lg flex-shrink-0">
                                    {{ $taskQueue->sequence_order }}
                                </div>

                                <!-- Product Info -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start justify-between mb-2">
                                        <div>
                                            <h3 class="text-lg font-semibold text-gray-900">{{ $taskQueue->product->name }}</h3>
                                            @if($taskQueue->product->description)
                                                <p class="text-sm text-gray-600 mt-1">{{ $taskQueue->product->description }}</p>
                                            @endif
                                        </div>
                                        <span class="ml-4 px-3 py-1 text-sm font-semibold rounded-full {{ status_badge_color($taskQueue->status) }}">
                                            {{ ucfirst($taskQueue->status) }}
                                        </span>
                                    </div>

                                    <!-- Product Details -->
                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4">
                                        <div class="bg-blue-50 rounded-lg p-3">
                                            <p class="text-xs text-gray-600">Required Points</p>
                                            <p class="text-lg font-bold text-blue-600">{{ number_format($taskQueue->product->base_points) }}</p>
                                        </div>
                                        <div class="bg-green-50 rounded-lg p-3">
                                            <p class="text-xs text-gray-600">Commission</p>
                                            <p class="text-lg font-bold text-green-600">{{ number_format($taskQueue->product->calculateCommission($user)) }}</p>
                                        </div>
                                        <div class="bg-purple-50 rounded-lg p-3">
                                            <p class="text-xs text-gray-600">Min Tier</p>
                                            <p class="text-sm font-semibold text-purple-600">{{ $taskQueue->product->minMembershipTier->name }}</p>
                                        </div>
                                        <div class="bg-gray-50 rounded-lg p-3">
                                            <p class="text-xs text-gray-600">Status</p>
                                            <p class="text-sm font-semibold text-gray-900">
                                                @if($taskQueue->status === 'completed')
                                                    Completed
                                                @elseif($taskQueue->status === 'active')
                                                    In Progress
                                                @else
                                                    Waiting
                                                @endif
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Timeline -->
                                    <div class="flex items-center space-x-6 mt-4 text-sm text-gray-600">
                                        <div class="flex items-center">
                                            <i class="fas fa-calendar-plus text-gray-400 mr-2"></i>
                                            <span>Assigned: {{ $taskQueue->assigned_at->format('M d, Y H:i') }}</span>
                                        </div>
                                        @if($taskQueue->activated_at)
                                            <div class="flex items-center">
                                                <i class="fas fa-play text-gray-400 mr-2"></i>
                                                <span>Started: {{ $taskQueue->activated_at->format('M d, Y H:i') }}</span>
                                            </div>
                                        @endif
                                        @if($taskQueue->completed_at)
                                            <div class="flex items-center">
                                                <i class="fas fa-check text-gray-400 mr-2"></i>
                                                <span>Completed: {{ $taskQueue->completed_at->format('M d, Y H:i') }}</span>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Can User Access? -->
                                    @if(!$taskQueue->product->isAccessibleBy($user))
                                        <div class="mt-3 bg-red-50 border border-red-200 rounded-lg p-3">
                                            <p class="text-sm text-red-800">
                                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                                User's membership level is insufficient for this task
                                            </p>
                                        </div>
                                    @elseif($taskQueue->status === 'queued' && $user->point_balance < $taskQueue->product->base_points)
                                        <div class="mt-3 bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                                            <p class="text-sm text-yellow-800">
                                                <i class="fas fa-exclamation-circle mr-2"></i>
                                                User needs {{ number_format($taskQueue->product->base_points - $user->point_balance) }} more points to start this task
                                            </p>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Actions -->
                            @if($taskQueue->status === 'queued')
                                <button onclick="removeFromQueue({{ $taskQueue->id }})" 
                                        class="ml-4 px-3 py-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition text-sm">
                                    <i class="fas fa-times mr-1"></i> Remove
                                </button>
                            @endif
                        </div>
                    </div>
                @endif
            @empty
                <div class="p-12 text-center">
                    <i class="fas fa-inbox text-6xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500 text-lg">No tasks in queue</p>
                    <a href="{{ route('admin.task-queue.index') }}" class="mt-4 inline-block text-primary hover:text-primary/80">
                        Assign tasks to this user
                    </a>
                </div>
            @endforelse
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    async function removeFromQueue(taskQueueId) {
        if (!confirm('Are you sure you want to remove this task from the queue?')) return;

        try {
            const response = await axios.delete(`/admin/task-queue/${taskQueueId}`);
            if (response.data.success) {
                location.reload();
            }
        } catch (error) {
            alert(error.response?.data?.message || 'Error removing task');
        }
    }
</script>
@endpush