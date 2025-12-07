@extends('layouts.user')

@section('title', 'Login')

@section('content')
<div class="flex items-center justify-center min-h-[calc(100vh-12rem)]">
    <div class="w-full max-w-md" x-data="loginPage()">
        
        <!-- Welcome Section -->
        <div class="text-center mb-6 text-black">
            <h1 class="text-2xl font-bold mb-1">WELCOME BACK</h1>
        </div>

        <!-- Login Card -->
        <div class="bg-gradient-to-br from-[#0a2540] via-[#0d3a5f] to-[#1a5080] rounded-3xl shadow-2xl p-6 border border-white border-opacity-10">
            
            <h2 class="text-base font-bold text-white mb-6">Member Login</h2>

            <!-- Login Form -->
            <form method="POST" 
                  action="{{ route('login') }}" 
                  class="space-y-4" 
                  @submit="handleSubmit">
                @csrf

                <!-- Username/Phone -->
                <div>
                    <label for="name" class="block text-white text-xs font-medium mb-2">
                        Username/Phone
                    </label>
                    <input type="text" 
                        id="name" 
                        name="name" 
                        value="{{ old('name') }}"
                        required 
                        autofocus
                        placeholder="Username/Phone"
                        class="block w-full px-3 py-2.5 bg-white bg-opacity-90 border-0 rounded-xl text-gray-900 placeholder-gray-500 text-sm focus:ring-2 focus:ring-orange-500 focus:bg-white transition">
                    @error('name')
                        <p class="text-red-300 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-white text-xs font-medium mb-2">
                        Password
                    </label>
                    <div class="relative">
                        <input :type="showPassword ? 'text' : 'password'" 
                            id="password" 
                            name="password" 
                            required
                            placeholder="Password"
                            class="block w-full px-3 py-2.5 pr-10 bg-white bg-opacity-90 border-0 rounded-xl text-gray-900 placeholder-gray-500 text-sm focus:ring-2 focus:ring-orange-500 focus:bg-white transition">
                        <button type="button" 
                                @click="showPassword = !showPassword"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700 focus:outline-none">
                            <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'" class="text-sm"></i>
                        </button>
                    </div>
                    @error('password')
                        <p class="text-red-300 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Forgot Password -->
                <div class="text-right">
                    <a @click.prevent="showCsPopup = true" 
                       href="#"
                       class="text-white text-xs underline hover:text-gray-200 transition cursor-pointer">
                        Forgot Password?
                    </a>
                </div>

                <!-- Submit Button -->
                <button type="submit"
                        :disabled="submitting"
                        :class="submitting ? 'opacity-50 cursor-not-allowed' : ''"
                        class="w-full gradient-button text-white py-2.5 px-6 rounded-xl font-bold text-sm shadow-lg transition">
                    <span x-show="!submitting">LOGIN</span>
                    <span x-show="submitting" x-cloak>
                        <i class="fas fa-spinner fa-spin mr-2"></i>Logging in...
                    </span>
                </button>

                <!-- Create Account Link -->
                <div class="text-center">
                    <p class="text-gray-300 text-xs">
                        Don't have an account? 
                        <a href="{{ route('register') }}" class="text-white underline ml-1 hover:text-gray-200 transition">
                            Create an account
                        </a>
                    </p>
                </div>
            </form>
        </div>
        @include('partials.customer-service-popup')
    </div>
</div>
@endsection

@push('scripts')
<script>
    function loginPage() {
        return {
            submitting: false,
            showCsPopup: false,
            showPassword: false,

            handleSubmit(e) {
                this.submitting = true;
                showLoading('Signing in...');
                // Form will submit normally, no need to prevent default
            }
        }
    }
</script>
@endpush