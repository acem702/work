@extends('layouts.user')

@section('title', 'Register')

@section('content')
<div class="flex items-center justify-center min-h-[calc(100vh-12rem)] py-6">
    <div class="w-full max-w-md">
        
        <!-- Welcome Section -->
        <div class="text-center mb-6 text-black">
            <h1 class="text-2xl font-bold mb-1">CREATE ACCOUNT</h1>
        </div>

        <!-- Registration Card -->
        <div class="bg-gradient-to-br from-[#0a2540] via-[#0d3a5f] to-[#1a5080] rounded-3xl shadow-2xl p-6 border border-white border-opacity-10">
            
            <h2 class="text-base font-bold text-white mb-6">Member Registration</h2>

            <!-- Registration Form -->
            <form method="POST" 
                  action="{{ route('register') }}" 
                  class="space-y-4" 
                  x-data="registrationForm()" 
                  @submit="handleSubmit">
                @csrf

                <!-- Username -->
                <div>
                    <label for="username" class="block text-white text-xs font-medium mb-2">
                        Username <span class="text-red-400">*</span>
                    </label>
                    <input type="text" 
                        id="name" 
                        name="name" 
                        value="{{ old('name') }}"
                        required 
                        autofocus
                        placeholder="Enter username"
                        class="block w-full px-3 py-2.5 bg-white bg-opacity-90 border-0 rounded-xl text-gray-900 placeholder-gray-500 text-sm focus:ring-2 focus:ring-orange-500 focus:bg-white transition">
                    @error('name')
                        <p class="text-red-300 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Phone -->
                <div>
                    <label for="phone" class="block text-white text-xs font-medium mb-2">
                        Phone Number <span class="text-red-400">*</span>
                    </label>
                    <input type="tel" 
                        id="phone" 
                        name="phone" 
                        value="{{ old('phone') }}"
                        required
                        placeholder="Enter phone number"
                        class="block w-full px-3 py-2.5 bg-white bg-opacity-90 border-0 rounded-xl text-gray-900 placeholder-gray-500 text-sm focus:ring-2 focus:ring-orange-500 focus:bg-white transition">
                    @error('phone')
                        <p class="text-red-300 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-white text-xs font-medium mb-2">
                        Password <span class="text-red-400">*</span>
                    </label>
                    <div class="relative">
                        <input :type="showPassword ? 'text' : 'password'" 
                            id="password" 
                            name="password" 
                            required
                            x-model="password"
                            placeholder="Enter password"
                            class="block w-full px-3 py-2.5 pr-10 bg-white bg-opacity-90 border-0 rounded-xl text-gray-900 placeholder-gray-500 text-sm focus:ring-2 focus:ring-orange-500 focus:bg-white transition">
                        <button type="button" 
                                @click="showPassword = !showPassword"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700">
                            <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'" class="text-sm"></i>
                        </button>
                    </div>
                    @error('password')
                        <p class="text-red-300 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="password_confirmation" class="block text-white text-xs font-medium mb-2">
                        Confirm Password <span class="text-red-400">*</span>
                    </label>
                    <div class="relative">
                        <input :type="showConfirmPassword ? 'text' : 'password'" 
                            id="password_confirmation" 
                            name="password_confirmation" 
                            required
                            x-model="passwordConfirmation"
                            placeholder="Re-enter password"
                            class="block w-full px-3 py-2.5 pr-10 bg-white bg-opacity-90 border-0 rounded-xl text-gray-900 placeholder-gray-500 text-sm focus:ring-2 focus:ring-orange-500 focus:bg-white transition">
                        <button type="button" 
                                @click="showConfirmPassword = !showConfirmPassword"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700">
                            <i :class="showConfirmPassword ? 'fas fa-eye-slash' : 'fas fa-eye'" class="text-sm"></i>
                        </button>
                    </div>
                    <p x-show="passwordConfirmation && password !== passwordConfirmation" 
                       x-cloak
                       class="text-red-300 text-xs mt-1">
                        Passwords do not match
                    </p>
                </div>

                <!-- Withdrawal Password -->
                <div>
                    <label for="withdrawal_password" class="block text-white text-xs font-medium mb-2">
                        Withdrawal Password <span class="text-red-400">*</span>
                    </label>
                    <div class="relative">
                        <input :type="showWithdrawalPassword ? 'text' : 'password'" 
                            id="withdrawal_password" 
                            name="withdrawal_password" 
                            required
                            placeholder="6-digit withdrawal PIN"
                            maxlength="6"
                            class="block w-full px-3 py-2.5 pr-10 bg-white bg-opacity-90 border-0 rounded-xl text-gray-900 placeholder-gray-500 text-sm focus:ring-2 focus:ring-orange-500 focus:bg-white transition">
                        <button type="button" 
                                @click="showWithdrawalPassword = !showWithdrawalPassword"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700">
                            <i :class="showWithdrawalPassword ? 'fas fa-eye-slash' : 'fas fa-eye'" class="text-sm"></i>
                        </button>
                    </div>
                    <p class="text-gray-300 text-xs mt-1">
                        <i class="fas fa-info-circle mr-1"></i>
                        Use this PIN for withdrawals
                    </p>
                    @error('withdrawal_password')
                        <p class="text-red-300 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Referral Code (Optional) -->
                <div>
                    <label for="referral_code" class="block text-white text-xs font-medium mb-2">
                        Referral Code <span class="text-red-400">*</span>
                    </label>
                    <input type="text" 
                        id="referral_code" 
                        name="referral_code" 
                        value="{{ old('referral_code') }}"
                        placeholder="Enter referral code"
                        class="block w-full px-3 py-2.5 bg-white bg-opacity-90 border-0 rounded-xl text-gray-900 placeholder-gray-500 text-sm focus:ring-2 focus:ring-orange-500 focus:bg-white transition">
                    @error('referral_code')
                        <p class="text-red-300 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Agreement Checkbox -->
                <div>
                    <label class="flex items-start space-x-2 cursor-pointer group">
                        <input type="checkbox" 
                               name="agreement" 
                               required
                               x-model="agreed"
                               class="mt-0.5 w-4 h-4 rounded border-gray-300 text-orange-500 focus:ring-orange-500 focus:ring-2">
                        <span class="text-white text-xs leading-relaxed">
                            I agree to the 
                            <a href="#" class="text-orange-300 underline hover:text-orange-200">Terms and Conditions</a> 
                            and 
                            <a href="#" class="text-orange-300 underline hover:text-orange-200">Privacy Policy</a>
                        </span>
                    </label>
                    @error('agreement')
                        <p class="text-red-300 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Button -->
                <button type="submit"
                        :disabled="submitting || !canSubmit"
                        :class="canSubmit ? '' : 'opacity-50 cursor-not-allowed'"
                        class="w-full gradient-button text-white py-2.5 px-6 rounded-xl font-bold text-sm shadow-lg transition">
                    <span x-show="!submitting">CREATE ACCOUNT</span>
                    <span x-show="submitting" x-cloak>
                        <i class="fas fa-spinner fa-spin mr-2"></i>Creating...
                    </span>
                </button>

                <!-- Login Link -->
                <div class="text-center">
                    <p class="text-gray-300 text-xs">
                        Already have an account? 
                        <a href="{{ route('login') }}" class="text-white underline ml-1 hover:text-gray-200 transition">
                            Sign in
                        </a>
                    </p>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function registrationForm() {
        return {
            submitting: false,
            showPassword: false,
            showConfirmPassword: false,
            showWithdrawalPassword: false,
            password: '',
            passwordConfirmation: '',
            agreed: false,

            get canSubmit() {
                return this.password && 
                       this.passwordConfirmation && 
                       this.password === this.passwordConfirmation && 
                       this.agreed;
            },

            handleSubmit(e) {
                if (!this.canSubmit) {
                    e.preventDefault();
                    
                    if (!this.agreed) {
                        showAlert('Please agree to the Terms and Conditions', 'error');
                        return;
                    }
                    
                    if (this.password !== this.passwordConfirmation) {
                        showAlert('Passwords do not match', 'error');
                        return;
                    }
                }
                
                this.submitting = true;
                showLoading('Creating your account...');
            }
        }
    }
</script>
@endpush