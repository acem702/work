@extends('layouts.user')

@section('title', 'Bind Wallet Address')

@section('content')
<div x-data="bindWalletPage()" class="space-y-6">
    
    <!-- Page Title -->
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900">Bind Wallet Address</h1>

        <a href="{{ route('dashboard') }}" 
        class="flex items-center space-x-2 px-4 py-2 bg-gradient-to-r from-orange-500 to-red-500 rounded-xl text-white text-sm font-bold shadow-lg hover:shadow-xl transition">
            <i class="fas fa-arrow-left"></i>
            <span>Dashboard</span>
        </a>
    </div>

    <!-- Orange Divider Line -->
    <div class="w-full h-1 bg-gradient-to-r from-orange-500 to-orange-400"></div>

    <!-- Current Wallet Info (if exists) -->
    @if(auth()->user()->withdrawal_address && auth()->user()->exchanger)
    <div class="bg-blue-50 border border-blue-200 rounded-2xl p-5">
        <div class="flex items-start space-x-3">
            <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fas fa-wallet text-white"></i>
            </div>
            <div class="flex-1">
                <h3 class="text-sm font-bold text-blue-900 mb-1">Current Withdrawal Method</h3>
                <p class="text-xs text-blue-700 mb-1">
                    <span class="font-semibold">Exchanger:</span> {{ auth()->user()->exchanger }}
                </p>
                <p class="text-xs text-blue-700 break-all">
                    <span class="font-semibold">Address:</span> {{ auth()->user()->withdrawal_address }}
                </p>
            </div>
        </div>
    </div>
    @endif

    <!-- Bind Wallet Form Card -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
        
        <div class="mb-6">
            <h2 class="text-lg font-bold text-gray-900 mb-2">
                @if(auth()->user()->withdrawal_address)
                    Update Withdrawal Method
                @else
                    Setup Withdrawal Method
                @endif
            </h2>
            <p class="text-sm text-gray-600">
                Bind your wallet address to enable withdrawals. This information will be used for all future withdrawal requests.
            </p>
        </div>

        <!-- Form -->
        <form @submit.prevent="submitForm" class="space-y-5">
            
            <!-- Exchanger (Single-line Input) -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Exchanger <span class="text-red-500">*</span>
                </label>

                <input type="text"
                    x-model="form.exchanger"
                    required
                    placeholder="Enter exchanger name"
                    class="block w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-sm">

                <p class="text-xs text-gray-500 mt-1">
                    <i class="fas fa-info-circle mr-1"></i>
                    Type your preferred withdrawal method manually.
                </p>
            </div>


            <!-- Wallet Address -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Wallet Address <span class="text-red-500">*</span>
                </label>
                <textarea x-model="form.withdrawal_address"
                          required
                          placeholder="Enter your wallet address or account details"
                          class="block w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 resize-none text-sm"></textarea>
                <p class="text-xs text-gray-500 mt-1">
                    <i class="fas fa-exclamation-triangle mr-1"></i>
                    Please double-check your address. Incorrect addresses may result in loss of funds.
                </p>
            </div>

            <!-- Important Notice -->
            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4">
                <div class="flex items-start space-x-3">
                    <i class="fas fa-shield-alt text-yellow-600 mt-0.5"></i>
                    <div>
                        <h4 class="text-sm font-bold text-yellow-900 mb-1">Important Security Notice</h4>
                        <ul class="text-xs text-yellow-800 space-y-1">
                            <li>• Ensure your wallet address is correct before submitting</li>
                            <li>• This information will be used for all withdrawal requests</li>
                            <li>• You can update this information anytime</li>
                            <li>• Never share your withdrawal password with anyone</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <button type="submit"
                    :disabled="submitting"
                    :class="submitting ? 'opacity-50 cursor-not-allowed' : ''"
                    class="w-full gradient-button text-white py-3 px-6 rounded-xl font-bold text-sm shadow-lg hover:shadow-xl transition">
                <span x-show="!submitting">
                    @if(auth()->user()->withdrawal_address)
                        <i class="fas fa-sync-alt mr-2"></i>Update Withdrawal Method
                    @else
                        <i class="fas fa-link mr-2"></i>Bind Wallet Address
                    @endif
                </span>
                <span x-show="submitting" x-cloak>
                    <i class="fas fa-spinner fa-spin mr-2"></i>Processing...
                </span>
            </button>
        </form>
    </div>

</div>
@endsection

@push('scripts')
<script>
    function bindWalletPage() {
        return {
            submitting: false,
            form: {
                exchanger: '{{ old("exchanger", auth()->user()->exchanger) }}',
                withdrawal_address: '{{ old("withdrawal_address", auth()->user()->withdrawal_address) }}'
            },

            async submitForm() {
                if (!this.form.exchanger || !this.form.withdrawal_address) {
                    showAlert('Please fill in all required fields', 'error');
                    return;
                }

                this.submitting = true;
                showLoading('Updating withdrawal method...');

                try {
                    const response = await axios.post('{{ route("withdrawal-method.update") }}', this.form);
                    
                    hideLoading();
                    showAlert('Withdrawal method updated successfully', 'success');
                    
                    // Reload page after 1 second to show updated info
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                    
                } catch (error) {
                    hideLoading();
                    if (error.response && error.response.data.errors) {
                        const errors = error.response.data.errors;
                        const firstError = Object.values(errors)[0][0];
                        showAlert(firstError, 'error');
                    } else {
                        showAlert('Failed to update withdrawal method', 'error');
                    }
                } finally {
                    this.submitting = false;
                }
            }
        }
    }
</script>
@endpush