@extends('layouts.admin')

@section('title', 'Users Management')

@section('content')
<div class="p-6">
    
    <!-- Page Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Users Management</h1>
            <p class="text-gray-600 mt-1">Manage all users and agents</p>
        </div>
        <button @click="$refs.createUserModal.showModal()" 
                class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition flex items-center">
            <i class="fas fa-plus mr-2"></i>
            Create User
        </button>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
        <form action="{{ route('admin.users.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Name or email..."
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                <select name="role" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                    <option value="">All Roles</option>
                    <option value="user" {{ request('role') === 'user' ? 'selected' : '' }}>User</option>
                    <option value="agent" {{ request('role') === 'agent' ? 'selected' : '' }}>Agent</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                    <option value="banned" {{ request('status') === 'banned' ? 'selected' : '' }}>Banned</option>
                </select>
            </div>

            <div class="flex items-end">
                <button type="submit" class="w-full px-4 py-2 bg-gray-900 text-white rounded-lg hover:bg-gray-800 transition">
                    <i class="fas fa-search mr-2"></i>Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Membership</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Balance</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Joined</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 rounded-full bg-primary flex items-center justify-center text-white font-semibold">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                        @if($user->referrer)
                                            <div class="text-xs text-gray-400">Ref: {{ $user->referrer->name }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $user->role === 'agent' ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ membership_badge_color($user->membershipTier->level) }}">
                                    {{ $user->membershipTier->name }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-semibold text-gray-900">{{ number_format($user->point_balance, 2) }}</div>
                                <div class="text-xs text-gray-500">points</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ status_badge_color($user->status) }}">
                                    {{ ucfirst($user->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $user->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end space-x-2">
                                    <a href="{{ route('admin.users.show', $user) }}" 
                                       class="text-blue-600 hover:text-blue-900" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <button onclick="openTopUpModal({{ $user->id }}, '{{ $user->name }}', {{ $user->point_balance }})" 
                                            class="text-green-600 hover:text-green-900" title="Top Up Points">
                                        <i class="fas fa-plus-circle"></i>
                                    </button>
                                    <button onclick="openStatusModal({{ $user->id }}, '{{ $user->name }}', '{{ $user->status }}')" 
                                            class="text-yellow-600 hover:text-yellow-900" title="Change Status">
                                        <i class="fas fa-toggle-on"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                <i class="fas fa-users text-4xl text-gray-300 mb-4"></i>
                                <p>No users found</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($users->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $users->links() }}
            </div>
        @endif
    </div>

</div>

<!-- Create User Modal -->
<dialog x-ref="createUserModal" class="rounded-xl shadow-2xl backdrop:bg-black backdrop:bg-opacity-50 w-full max-w-md">
    <div class="p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-bold text-gray-900">Create New User</h2>
            <button onclick="this.closest('dialog').close()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form id="createUserForm" class="space-y-4">
            @csrf
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                <input type="text" name="name" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
</div>
<div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input type="email" name="email" required
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
            <input type="password" name="password" required minlength="8"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
            <select name="role" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                <option value="user">User</option>
                <option value="agent">Agent</option>
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Membership Tier</label>
            <select name="membership_tier_id" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                @foreach(\App\Models\MembershipTier::active()->ordered()->get() as $tier)
                    <option value="{{ $tier->id }}">{{ $tier->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Initial Points (Optional)</label>
            <input type="number" name="initial_points" min="0" step="0.01" value="0"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Referrer Code (Optional)</label>
            <input type="text" name="referrer_code"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
        </div>

        <div class="flex justify-end space-x-3 pt-4">
            <button type="button" onclick="this.closest('dialog').close()"
                    class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                Cancel
            </button>
            <button type="submit"
                    class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition">
                Create User
            </button>
        </div>
    </form>
</div>
</dialog>
<!-- Top Up Modal -->
<dialog id="topUpModal" class="rounded-xl shadow-2xl backdrop:bg-black backdrop:bg-opacity-50 w-full max-w-md">
    <div class="p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-bold text-gray-900">Top Up Points</h2>
            <button onclick="this.closest('dialog').close()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="topUpForm" class="space-y-4">
        @csrf
        <input type="hidden" id="topUpUserId" name="user_id">
        
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
            <p class="text-sm text-gray-700">User: <span id="topUpUserName" class="font-semibold"></span></p>
            <p class="text-sm text-gray-700">Current Balance: <span id="topUpCurrentBalance" class="font-semibold"></span> points</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Amount</label>
            <input type="number" name="amount" required min="0.01" step="0.01"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Reason</label>
            <textarea name="reason" required rows="3"
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"></textarea>
        </div>

        <div class="flex justify-end space-x-3 pt-4">
            <button type="button" onclick="this.closest('dialog').close()"
                    class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                Cancel
            </button>
            <button type="submit"
                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                Add Points
            </button>
        </div>
    </form>
</div>
</dialog>
<!-- Status Change Modal -->
<dialog id="statusModal" class="rounded-xl shadow-2xl backdrop:bg-black backdrop:bg-opacity-50 w-full max-w-md">
    <div class="p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-bold text-gray-900">Change User Status</h2>
            <button onclick="this.closest('dialog').close()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="statusForm" class="space-y-4">
        @csrf
        <input type="hidden" id="statusUserId" name="user_id">
        
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
            <p class="text-sm text-gray-700">User: <span id="statusUserName" class="font-semibold"></span></p>
            <p class="text-sm text-gray-700">Current Status: <span id="statusCurrentStatus" class="font-semibold"></span></p>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">New Status</label>
            <select name="status" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                <option value="active">Active</option>
                <option value="suspended">Suspended</option>
                <option value="banned">Banned</option>
            </select>
        </div>

        <div class="flex justify-end space-x-3 pt-4">
            <button type="button" onclick="this.closest('dialog').close()"
                    class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                Cancel
            </button>
            <button type="submit"
                    class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition">
                Update Status
            </button>
        </div>
    </form>
</div>
</dialog>
@endsection
@push('scripts')
<script>
    // Create User Form
    document.getElementById('createUserForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(e.target);
        
        try {
            const response = await axios.post('{{ route("admin.users.store") }}', formData);
            if (response.data.success) {
                location.reload();
            }
        } catch (error) {
            alert(error.response?.data?.message || 'Error creating user');
        }
    });

    // Top Up Modal Functions
    function openTopUpModal(userId, userName, balance) {
        document.getElementById('topUpUserId').value = userId;
        document.getElementById('topUpUserName').textContent = userName;
        document.getElementById('topUpCurrentBalance').textContent = parseFloat(balance).toFixed(2);
        document.getElementById('topUpModal').showModal();
    }

    document.getElementById('topUpForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const userId = document.getElementById('topUpUserId').value;
        const formData = new FormData(e.target);
        
        try {
            const response = await axios.post(`/admin/users/${userId}/topup`, formData);
            if (response.data.success) {
                location.reload();
            }
        } catch (error) {
            alert(error.response?.data?.message || 'Error adding points');
        }
    });

    // Status Change Modal Functions
    function openStatusModal(userId, userName, currentStatus) {
        document.getElementById('statusUserId').value = userId;
        document.getElementById('statusUserName').textContent = userName;
        document.getElementById('statusCurrentStatus').textContent = currentStatus;
        document.getElementById('statusModal').showModal();
    }

    document.getElementById('statusForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const userId = document.getElementById('statusUserId').value;
        const formData = new FormData(e.target);
        
        try {
            const response = await axios.post(`/admin/users/${userId}/status`, formData);
            if (response.data.success) {
                location.reload();
            }
        } catch (error) {
            alert(error.response?.data?.message || 'Error updating status');
        }
    });
</script>
@endpush