@extends('layouts.user')

@section('title', 'Withdraw Funds')

@section('content')
<div x-data="withdrawPage()" class="space-y-6">
    
    <!-- Page Title -->
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Withdraw Funds</h1>
    </div>

    <!-- Orange Divider Line -->
    <div class="w-full h-1 bg-gradient-to-r from-orange-500 to-orange-400"></div>

    <!-- Available Balance Card -->
    <div class="bg-gradient-to-br from-green-600 via-green-500 to-teal-500 rounded-2xl shadow-xl p-6">
        <div class="text-center">
            <p class="text-sm text-white text-opacity-90 mb-2">Available Balance</p>
            <p class="text-4xl font-bold text-white mb-1">
                $<span x-text="availableBalance"></span>
            </p>
            <p class="text-xs text-white text-opacity-80">USD</p>
        </div>
    </div>

    <!-- Withdrawal Method Status -->
    @if(!auth()->user()->withdrawal_address || !auth()->user()->exchanger)
    <div class="bg-red-50 border border-red-200 rounded-2xl p-5">
        <div class="flex items-start space-x-3">
            <div class="w-10 h-10 bg-red-500 rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fas fa-exclamation-triangle text-white"></i>
            </div>
            <div class="flex-1">
                <h3 class="text-sm font-bold text-red-900 mb-2">Withdrawal Method Not Set</h3>
                <p class="text-xs text-red-700 mb-3">
                    You need to bind your wallet address before making a withdrawal request.
                </p>
                <a href="{{ route('withdrawal-method.index') }}" 
                   class="inline-block px-4 py-2 bg-red-600 text-white text-xs font-semibold rounded-lg hover:bg-red-700 transition">
                    <i class="fas fa-link mr-1"></i>Bind Wallet Now
                </a>
            </div>
        </div>
    </div>
    @else
    <!-- Current Withdrawal Method -->
    <div class="bg-blue-50 border border-blue-200 rounded-2xl p-5">
        <div class="flex items-start space-x-3">
            <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fas fa-wallet text-white"></i>
            </div>
            <div class="flex-1">
                <h3 class="text-sm font-bold text-blue-900 mb-1">Withdrawal Method</h3>
                <p class="text-xs text-blue-700 mb-1">
                    <span class="font-semibold">Exchanger:</span> {{ auth()->user()->exchanger }}
                </p>
                <p class="text-xs text-blue-700 break-all">
                    <span class="font-semibold">Address:</span> {{ auth()->user()->withdrawal_address }}
                </p>
                <a href="{{ route('withdrawal-method.index') }}" 
                   class="text-xs text-blue-600 hover:text-blue-800 underline mt-2 inline-block">
                    Change withdrawal method
                </a>
            </div>
        </div>
    </div>
    @endif

    <!-- Withdrawal Form Card -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
        
        <div class="mb-6">
            <h2 class="text-lg font-bold text-gray-900 mb-2">Request Withdrawal</h2>
            <p class="text-sm text-gray-600">
                Enter the amount you wish to withdraw and confirm with your withdrawal password.
            </p>
        </div>

        <!-- Form -->
        <form @submit.prevent="submitWithdrawal" class="space-y-5">
            
            <!-- Amount Field -->
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-sm font-semibold text-gray-700">
                        Withdrawal Amount (USD) <span class="text-red-500">*</span>
                    </label>
                    <button type="button" 
                            @click="setMaxAmount"
                            class="text-xs font-bold text-orange-500 hover:text-orange-600 transition">
                        ALL
                    </button>
                </div>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-semibold">$</span>
                    <input type="number" 
                           x-model="form.amount"
                           required
                           step="0.01"
                           min="10"
                           :max="availableBalance"
                           placeholder="0.00"
                           class="block w-full pl-8 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-sm font-semibold">
                </div>
                <div class="flex items-center justify-between mt-2">
                    <p class="text-xs text-gray-500">
                        <i class="fas fa-info-circle mr-1"></i>
                        Minimum: $10.00
                    </p>
                    <p class="text-xs text-gray-600 font-semibold">
                        Available: $<span x-text="availableBalance"></span>
                    </p>
                </div>
            </div>

            <!-- Withdrawal Fee Info -->
            <div class="bg-gray-50 rounded-xl p-4 space-y-2">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Withdrawal Amount:</span>
                    <span class="font-semibold text-gray-900">$<span x-text="form.amount || '0.00'"></span></span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Processing Fee:</span>
                    <span class="font-semibold text-gray-900">$0.00</span>
                </div>
                <div class="border-t border-gray-200 pt-2 flex justify-between">
                    <span class="text-sm font-bold text-gray-900">You Will Receive:</span>
                    <span class="text-lg font-bold text-green-600">$<span x-text="form.amount || '0.00'"></span></span>
                </div>
            </div>

            <!-- Withdrawal Password -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Withdrawal Password <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <input :type="showPassword ? 'text' : 'password'" 
                           x-model="form.withdrawal_password"
                           required
                           maxlength="6"
                           placeholder="Enter 6-digit PIN"
                           class="block w-full px-4 py-3 pr-10 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-sm">
                    <button type="button" 
                            @click="showPassword = !showPassword"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700">
                        <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                    </button>
                </div>
                <p class="text-xs text-gray-500 mt-1">
                    <i class="fas fa-shield-alt mr-1"></i>
                    Your 6-digit withdrawal PIN for security verification
                </p>
            </div>

            <!-- Important Notice -->
            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4">
                <div class="flex items-start space-x-3">
                    <i class="fas fa-clock text-yellow-600 mt-0.5"></i>
                    <div>
                        <h4 class="text-sm font-bold text-yellow-900 mb-1">Processing Information</h4>
                        <ul class="text-xs text-yellow-800 space-y-1">
                            <li>• Withdrawals are processed within 24-48 hours</li>
                            <li>• You can only have one pending withdrawal at a time</li>
                            <li>• Funds will be sent to your bound wallet address</li>
                            <li>• Contact support if you have any issues</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <button type="submit"
                    :disabled="submitting || !canSubmit"
                    :class="(submitting || !canSubmit) ? 'opacity-50 cursor-not-allowed' : ''"
                    class="w-full gradient-button text-white py-3 px-6 rounded-xl font-bold text-sm shadow-lg hover:shadow-xl transition">
                <span x-show="!submitting">
                    <i class="fas fa-paper-plane mr-2"></i>Submit Withdrawal Request
                </span>
                <span x-show="submitting" x-cloak>
                    <i class="fas fa-spinner fa-spin mr-2"></i>Processing...
                </span>
            </button>
        </form>
    </div>

    <!-- Recent Withdrawals -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
        <h2 class="text-lg font-bold text-gray-900 mb-4">Recent Withdrawals</h2>
        
        <div class="space-y-3">
            @forelse($withdrawals ?? [] as $withdrawal)
            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center
                                {{ $withdrawal->status === 'completed' ? 'bg-green-100' : 
                                   ($withdrawal->status === 'pending' ? 'bg-yellow-100' : 'bg-red-100') }}">
                        <i class="fas {{ $withdrawal->status === 'completed' ? 'fa-check text-green-600' : 
                                         ($withdrawal->status === 'pending' ? 'fa-clock text-yellow-600' : 'fa-times text-red-600') }}"></i>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-gray-900">${{ number_format($withdrawal->amount, 2) }}</p>
                        <p class="text-xs text-gray-500">{{ $withdrawal->created_at->format('M d, Y h:i A') }}</p>
                    </div>
                </div>
                <span class="px-3 py-1 rounded-full text-xs font-semibold
                            {{ $withdrawal->status === 'completed' ? 'bg-green-100 text-green-700' : 
                               ($withdrawal->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                    {{ ucfirst($withdrawal->status) }}
                </span>
            </div>
            @empty
            <div class="text-center py-8">
                <i class="fas fa-inbox text-gray-300 text-4xl mb-3"></i>
                <p class="text-gray-500 text-sm">No withdrawal history yet</p>
            </div>
            @endforelse
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    function withdrawPage() {
        return {
            submitting: false,
            showPassword: false,
            availableBalance: {{ auth()->user()->point_balance }},
            form: {
                amount: '',
                withdrawal_password: ''
            },

            get canSubmit() {
                return this.form.amount && 
                       this.form.withdrawal_password && 
                       this.form.withdrawal_password.length === 6 &&
                       parseFloat(this.form.amount) >= 10 &&
                       parseFloat(this.form.amount) <= this.availableBalance;
            },

            setMaxAmount() {
                this.form.amount = this.availableBalance.toFixed(2);
            },

            async submitWithdrawal() {
                if (!this.canSubmit) {
                    if (parseFloat(this.form.amount) < 10) {
                        showAlert('Minimum withdrawal amount is $10', 'error');
                    } else if (parseFloat(this.form.amount) > this.availableBalance) {
                        showAlert('Insufficient balance', 'error');
                    }
                    return;
                }

                this.submitting = true;
                showLoading('Processing withdrawal request...');

                try {
                    const response = await axios.post('{{ route("withdrawals.store") }}', this.form);
                    
                    hideLoading();
                    showAlert('Withdrawal request submitted successfully', 'success');
                    
                    // Reset form
                    this.form = {
                        amount: '',
                        withdrawal_password: ''
                    };
                    
                    // Reload page after 2 seconds to show updated balance and history
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                    
                } catch (error) {
                    hideLoading();
                    if (error.response && error.response.data.errors) {
                        const errors = error.response.data.errors;
                        const firstError = Object.values(errors)[0][0];
                        showAlert(firstError, 'error');
                    } else {
                        showAlert('Failed to process withdrawal request', 'error');
                    }
                } finally {
                    this.submitting = false;
                }
            }
        }
    }
</script>
@endpush