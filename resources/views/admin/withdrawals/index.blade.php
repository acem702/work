@extends('layouts.admin')

@section('title', 'Withdrawal Requests')

@section('content')
<div class="p-6">
    
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Withdrawal Requests</h1>
        <p class="text-gray-600 mt-1">Manage user withdrawal requests</p>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-yellow-600 mb-1">Pending Requests</p>
                    <p class="text-2xl font-bold text-yellow-900">{{ $stats['pending_count'] }}</p>
                    <p class="text-xs text-yellow-600 mt-1">${{ number_format($stats['pending_amount'], 2) }}</p>
                </div>
                <div class="w-12 h-12 bg-yellow-500 rounded-full flex items-center justify-center">
                    <i class="fas fa-clock text-white text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-green-50 border border-green-200 rounded-xl p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-green-600 mb-1">Completed Today</p>
                    <p class="text-2xl font-bold text-green-900">{{ $stats['completed_today'] }}</p>
                    <p class="text-xs text-green-600 mt-1">${{ number_format($stats['completed_today_amount'], 2) }}</p>
                </div>
                <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center">
                    <i class="fas fa-check-circle text-white text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Tabs -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6">
        <div class="flex border-b border-gray-200">
            <a href="{{ route('admin.withdrawals.index', ['status' => 'all']) }}" 
               class="px-6 py-4 text-sm font-medium {{ $status === 'all' ? 'text-primary border-b-2 border-primary' : 'text-gray-600 hover:text-gray-900' }}">
                All Requests
            </a>
            <a href="{{ route('admin.withdrawals.index', ['status' => 'pending']) }}" 
               class="px-6 py-4 text-sm font-medium {{ $status === 'pending' ? 'text-primary border-b-2 border-primary' : 'text-gray-600 hover:text-gray-900' }}">
                Pending
            </a>
            <a href="{{ route('admin.withdrawals.index', ['status' => 'completed']) }}" 
               class="px-6 py-4 text-sm font-medium {{ $status === 'completed' ? 'text-primary border-b-2 border-primary' : 'text-gray-600 hover:text-gray-900' }}">
                Completed
            </a>
            <a href="{{ route('admin.withdrawals.index', ['status' => 'rejected']) }}" 
               class="px-6 py-4 text-sm font-medium {{ $status === 'rejected' ? 'text-primary border-b-2 border-primary' : 'text-gray-600 hover:text-gray-900' }}">
                Rejected
            </a>
        </div>
    </div>

    <!-- Withdrawals Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Method</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Address</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Requested</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($withdrawals as $withdrawal)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            #{{ $withdrawal->id }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $withdrawal->user->name }}</div>
                            <div class="text-xs text-gray-500">{{ $withdrawal->user->email }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">
                            ${{ number_format($withdrawal->amount, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $withdrawal->exchanger }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            <div class="max-w-xs truncate" title="{{ $withdrawal->withdrawal_address }}">
                                {{ $withdrawal->withdrawal_address }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($withdrawal->status === 'pending')
                                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-700">
                                    Pending
                                </span>
                            @elseif($withdrawal->status === 'completed')
                                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700">
                                    Completed
                                </span>
                            @else
                                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-700">
                                    Rejected
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $withdrawal->requested_at->format('M d, Y h:i A') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @if($withdrawal->status === 'pending')
                                <div class="flex space-x-2">
                                    <button onclick="approveWithdrawal({{ $withdrawal->id }})"
                                            class="px-3 py-1 bg-green-500 text-white rounded-lg hover:bg-green-600 text-xs font-semibold">
                                        <i class="fas fa-check mr-1"></i>Approve
                                    </button>
                                    <button onclick="rejectWithdrawal({{ $withdrawal->id }})"
                                            class="px-3 py-1 bg-red-500 text-white rounded-lg hover:bg-red-600 text-xs font-semibold">
                                        <i class="fas fa-times mr-1"></i>Reject
                                    </button>
                                </div>
                            @else
                                <span class="text-gray-400 text-xs">
                                    {{ $withdrawal->status === 'completed' ? 'Processed' : 'Rejected' }}
                                </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-inbox text-4xl mb-3 text-gray-300"></i>
                            <p>No withdrawal requests found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($withdrawals->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $withdrawals->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-xl p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Reject Withdrawal Request</h3>
        
        <form id="rejectForm">
            <input type="hidden" id="rejectWithdrawalId">
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Reason for Rejection *</label>
                <textarea id="rejectReason" 
                          rows="4" 
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                          placeholder="Enter reason for rejection..."></textarea>
            </div>

            <div class="flex space-x-3">
                <button type="button" 
                        onclick="closeRejectModal()"
                        class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                    Cancel
                </button>
                <button type="submit" 
                        class="flex-1 px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">
                    Reject
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function approveWithdrawal(id) {
        if (!confirm('Are you sure you want to approve this withdrawal request?')) {
            return;
        }

        axios.post(`/admin/withdrawals/${id}/approve`)
            .then(response => {
                if (response.data.success) {
                    alert(response.data.message);
                    window.location.reload();
                }
            })
            .catch(error => {
                alert('Failed to approve withdrawal: ' + (error.response?.data?.message || 'Unknown error'));
            });
    }

    function rejectWithdrawal(id) {
        document.getElementById('rejectWithdrawalId').value = id;
        document.getElementById('rejectModal').classList.remove('hidden');
    }

    function closeRejectModal() {
        document.getElementById('rejectModal').classList.add('hidden');
        document.getElementById('rejectForm').reset();
    }

    document.getElementById('rejectForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const id = document.getElementById('rejectWithdrawalId').value;
        const reason = document.getElementById('rejectReason').value;

        if (!reason.trim()) {
            alert('Please enter a reason for rejection');
            return;
        }

        axios.post(`/admin/withdrawals/${id}/reject`, { reason })
            .then(response => {
                if (response.data.success) {
                    alert(response.data.message);
                    closeRejectModal();
                    window.location.reload();
                }
            })
            .catch(error => {
                alert('Failed to reject withdrawal: ' + (error.response?.data?.message || 'Unknown error'));
            });
    });
</script>
@endpush