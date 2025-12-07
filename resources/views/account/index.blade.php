@extends('layouts.user')

@section('title', 'My Account')

@section('content')
<div x-data="accountPage()" class="space-y-6">
    
    <!-- Page Title -->
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900">My Account</h1>

        <a href="{{ route('dashboard') }}" 
        class="flex items-center space-x-2 px-4 py-2 bg-gradient-to-r from-orange-500 to-red-500 rounded-xl text-white text-sm font-bold shadow-lg hover:shadow-xl transition">
            <i class="fas fa-arrow-left"></i>
            <span>Dashboard</span>
        </a>
    </div>

    <!-- Orange Divider Line -->
    <div class="w-full h-1 bg-gradient-to-r from-orange-500 to-orange-400"></div>

    <!-- Account Balance Card -->
    <div class="bg-gradient-to-br from-blue-900 via-blue-800 to-green-600 rounded-2xl shadow-xl p-6 text-center">
        <p class="text-sm text-gray-200 mb-2">Account Balance</p>
        <p class="text-4xl font-bold text-white mb-4">
            {{ number_format(auth()->user()->point_balance, 2) }} 
            <span class="text-base">(USD)</span>
        </p>
        <button onclick="window.location='{{ route(name: 'recharge') }}'" class="w-full max-w-xs mx-auto gradient-button text-white py-3 px-6 rounded-xl font-bold text-sm shadow-lg">
            Recharge
        </button>
    </div>

    <!-- User Info Card -->
    <div class="bg-white rounded-2xl shadow-lg p-5 border border-gray-100 space-y-3">
        <div>
            <p class="text-xs text-gray-500 mb-1">Username</p>
            <h3 class="text-lg font-bold text-gray-900">{{ auth()->user()->name }}</h3>
        </div>
        <div class="border-t border-gray-100 pt-3">
            <p class="text-xs text-gray-500 mb-1">Phone Number</p>
            <h3 class="text-lg font-bold text-gray-900">{{ auth()->user()->phone }}</h3>
        </div>
    </div>

    <!-- Password Options -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        
        <!-- Change Password Button -->
        <button @click="showPasswordModal = true" 
                class="bg-white rounded-2xl shadow-lg p-5 border border-gray-100 hover:border-orange-500 transition group text-left">
            <div class="flex items-center justify-between">
                <div>
                    <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center mb-3 group-hover:bg-orange-200 transition">
                        <i class="fas fa-lock text-orange-500 text-xl"></i>
                    </div>
                    <h3 class="text-base font-bold text-gray-900 mb-1">Change Password</h3>
                    <p class="text-xs text-gray-500">Update your login password</p>
                </div>
                <i class="fas fa-chevron-right text-gray-400 group-hover:text-orange-500 transition"></i>
            </div>
        </button>

        <!-- Change Withdrawal Password Button -->
        <button @click="showWithdrawalPasswordModal = true" 
                class="bg-white rounded-2xl shadow-lg p-5 border border-gray-100 hover:border-blue-500 transition group text-left">
            <div class="flex items-center justify-between">
                <div>
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mb-3 group-hover:bg-blue-200 transition">
                        <i class="fas fa-shield-alt text-blue-500 text-xl"></i>
                    </div>
                    <h3 class="text-base font-bold text-gray-900 mb-1">Withdrawal Password</h3>
                    <p class="text-xs text-gray-500">Update your 6-digit PIN</p>
                </div>
                <i class="fas fa-chevron-right text-gray-400 group-hover:text-blue-500 transition"></i>
            </div>
        </button>

    </div>

    <!-- Change Password Modal -->
    <div x-show="showPasswordModal" 
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center px-4"
         style="background: rgba(0, 0, 0, 0.6);"
         @click.self="showPasswordModal = false">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform scale-90"
             x-transition:enter-end="opacity-100 transform scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform scale-100"
             x-transition:leave-end="opacity-0 transform scale-90">
            
            <!-- Modal Header -->
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-900">Change Password</h3>
                <button @click="showPasswordModal = false" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <!-- Password Form -->
            <form @submit.prevent="updatePassword" class="space-y-4">
                
                <!-- Current Password -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Current Password
                    </label>
                    <div class="relative">
                        <input :type="showCurrentPassword ? 'text' : 'password'" 
                               x-model="passwordForm.current_password"
                               required
                               placeholder="Enter current password"
                               class="block w-full px-4 py-2.5 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                        <button type="button" 
                                @click="showCurrentPassword = !showCurrentPassword"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700">
                            <i :class="showCurrentPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                        </button>
                    </div>
                </div>

                <!-- New Password -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        New Password
                    </label>
                    <div class="relative">
                        <input :type="showNewPassword ? 'text' : 'password'" 
                               x-model="passwordForm.password"
                               required
                               placeholder="Enter new password"
                               class="block w-full px-4 py-2.5 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                        <button type="button" 
                                @click="showNewPassword = !showNewPassword"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700">
                            <i :class="showNewPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                        </button>
                    </div>
                </div>

                <!-- Confirm Password -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Confirm New Password
                    </label>
                    <div class="relative">
                        <input :type="showConfirmPassword ? 'text' : 'password'" 
                               x-model="passwordForm.password_confirmation"
                               required
                               placeholder="Re-enter new password"
                               class="block w-full px-4 py-2.5 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                        <button type="button" 
                                @click="showConfirmPassword = !showConfirmPassword"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700">
                            <i :class="showConfirmPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                        </button>
                    </div>
                    <p x-show="passwordForm.password && passwordForm.password_confirmation && passwordForm.password !== passwordForm.password_confirmation" 
                       x-cloak
                       class="text-red-500 text-xs mt-1">
                        Passwords do not match
                    </p>
                </div>

                <!-- Submit Buttons -->
                <div class="flex space-x-3 pt-2">
                    <button type="button" 
                            @click="showPasswordModal = false" 
                            class="flex-1 px-4 py-2.5 border-2 border-gray-300 text-gray-700 rounded-xl font-semibold text-sm hover:bg-gray-50 transition">
                        Cancel
                    </button>
                    <button type="submit" 
                            :disabled="submitting || passwordForm.password !== passwordForm.password_confirmation"
                            :class="(submitting || passwordForm.password !== passwordForm.password_confirmation) ? 'opacity-50 cursor-not-allowed' : ''"
                            class="flex-1 px-4 py-2.5 bg-gradient-to-r from-orange-500 to-red-500 text-white rounded-xl font-semibold text-sm hover:shadow-lg transition">
                        <span x-show="!submitting">Update Password</span>
                        <span x-show="submitting" x-cloak>
                            <i class="fas fa-spinner fa-spin mr-2"></i>Updating...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Change Withdrawal Password Modal -->
    <div x-show="showWithdrawalPasswordModal" 
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center px-4"
         style="background: rgba(0, 0, 0, 0.6);"
         @click.self="showWithdrawalPasswordModal = false">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform scale-90"
             x-transition:enter-end="opacity-100 transform scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform scale-100"
             x-transition:leave-end="opacity-0 transform scale-90">
            
            <!-- Modal Header -->
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-900">Change Withdrawal Password</h3>
                <button @click="showWithdrawalPasswordModal = false" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <!-- Withdrawal Password Form -->
            <form @submit.prevent="updateWithdrawalPassword" class="space-y-4">
                
                <!-- Current Withdrawal Password -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Current Withdrawal Password
                    </label>
                    <div class="relative">
                        <input :type="showCurrentWithdrawalPassword ? 'text' : 'password'" 
                               x-model="withdrawalPasswordForm.current_withdrawal_password"
                               required
                               maxlength="6"
                               placeholder="Enter current 6-digit PIN"
                               class="block w-full px-4 py-2.5 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <button type="button" 
                                @click="showCurrentWithdrawalPassword = !showCurrentWithdrawalPassword"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700">
                            <i :class="showCurrentWithdrawalPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                        </button>
                    </div>
                </div>

                <!-- New Withdrawal Password -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        New Withdrawal Password
                    </label>
                    <div class="relative">
                        <input :type="showNewWithdrawalPassword ? 'text' : 'password'" 
                               x-model="withdrawalPasswordForm.withdrawal_password"
                               required
                               maxlength="6"
                               placeholder="Enter new 6-digit PIN"
                               class="block w-full px-4 py-2.5 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <button type="button" 
                                @click="showNewWithdrawalPassword = !showNewWithdrawalPassword"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700">
                            <i :class="showNewWithdrawalPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                        </button>
                    </div>
                    <p class="text-gray-500 text-xs mt-1">
                        <i class="fas fa-info-circle mr-1"></i>
                        Must be exactly 6 digits
                    </p>
                </div>

                <!-- Confirm Withdrawal Password -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Confirm New Withdrawal Password
                    </label>
                    <div class="relative">
                        <input :type="showConfirmWithdrawalPassword ? 'text' : 'password'" 
                               x-model="withdrawalPasswordForm.withdrawal_password_confirmation"
                               required
                               maxlength="6"
                               placeholder="Re-enter new 6-digit PIN"
                               class="block w-full px-4 py-2.5 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <button type="button" 
                                @click="showConfirmWithdrawalPassword = !showConfirmWithdrawalPassword"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700">
                            <i :class="showConfirmWithdrawalPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                        </button>
                    </div>
                    <p x-show="withdrawalPasswordForm.withdrawal_password && withdrawalPasswordForm.withdrawal_password_confirmation && withdrawalPasswordForm.withdrawal_password !== withdrawalPasswordForm.withdrawal_password_confirmation" 
                       x-cloak
                       class="text-red-500 text-xs mt-1">
                        PINs do not match
                    </p>
                </div>

                <!-- Submit Buttons -->
                <div class="flex space-x-3 pt-2">
                    <button type="button" 
                            @click="showWithdrawalPasswordModal = false" 
                            class="flex-1 px-4 py-2.5 border-2 border-gray-300 text-gray-700 rounded-xl font-semibold text-sm hover:bg-gray-50 transition">
                        Cancel
                    </button>
                    <button type="submit" 
                            :disabled="submitting || withdrawalPasswordForm.withdrawal_password !== withdrawalPasswordForm.withdrawal_password_confirmation"
                            :class="(submitting || withdrawalPasswordForm.withdrawal_password !== withdrawalPasswordForm.withdrawal_password_confirmation) ? 'opacity-50 cursor-not-allowed' : ''"
                            class="flex-1 px-4 py-2.5 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl font-semibold text-sm hover:shadow-lg transition">
                        <span x-show="!submitting">Update PIN</span>
                        <span x-show="submitting" x-cloak>
                            <i class="fas fa-spinner fa-spin mr-2"></i>Updating...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    function accountPage() {
        return {
            showPasswordModal: false,
            showWithdrawalPasswordModal: false,
            submitting: false,
            
            // Password visibility toggles
            showCurrentPassword: false,
            showNewPassword: false,
            showConfirmPassword: false,
            showCurrentWithdrawalPassword: false,
            showNewWithdrawalPassword: false,
            showConfirmWithdrawalPassword: false,
            
            // Password form data
            passwordForm: {
                current_password: '',
                password: '',
                password_confirmation: ''
            },
            
            // Withdrawal password form data
            withdrawalPasswordForm: {
                current_withdrawal_password: '',
                withdrawal_password: '',
                withdrawal_password_confirmation: ''
            },

            async updatePassword() {
                if (this.passwordForm.password !== this.passwordForm.password_confirmation) {
                    showAlert('Passwords do not match', 'error');
                    return;
                }

                this.submitting = true;
                showLoading('Updating password...');

                try {
                    const response = await axios.post('{{ route("password.update") }}', this.passwordForm);
                    
                    hideLoading();
                    showAlert('Password updated successfully', 'success');
                    
                    // Reset form and close modal
                    this.passwordForm = {
                        current_password: '',
                        password: '',
                        password_confirmation: ''
                    };
                    this.showPasswordModal = false;
                    
                } catch (error) {
                    hideLoading();
                    if (error.response && error.response.data.errors) {
                        const errors = error.response.data.errors;
                        const firstError = Object.values(errors)[0][0];
                        showAlert(firstError, 'error');
                    } else {
                        showAlert('Failed to update password', 'error');
                    }
                } finally {
                    this.submitting = false;
                }
            },

            async updateWithdrawalPassword() {
                if (this.withdrawalPasswordForm.withdrawal_password !== this.withdrawalPasswordForm.withdrawal_password_confirmation) {
                    showAlert('PINs do not match', 'error');
                    return;
                }

                if (this.withdrawalPasswordForm.withdrawal_password.length !== 6) {
                    showAlert('Withdrawal PIN must be exactly 6 digits', 'error');
                    return;
                }

                this.submitting = true;
                showLoading('Updating withdrawal password...');

                try {
                    const response = await axios.post('{{ route("withdrawal-password.update") }}', this.withdrawalPasswordForm);
                    
                    hideLoading();
                    showAlert('Withdrawal password updated successfully', 'success');
                    
                    // Reset form and close modal
                    this.withdrawalPasswordForm = {
                        current_withdrawal_password: '',
                        withdrawal_password: '',
                        withdrawal_password_confirmation: ''
                    };
                    this.showWithdrawalPasswordModal = false;
                    
                } catch (error) {
                    hideLoading();
                    if (error.response && error.response.data.errors) {
                        const errors = error.response.data.errors;
                        const firstError = Object.values(errors)[0][0];
                        showAlert(firstError, 'error');
                    } else {
                        showAlert('Failed to update withdrawal password', 'error');
                    }
                } finally {
                    this.submitting = false;
                }
            }
        }
    }
</script>
@endpush