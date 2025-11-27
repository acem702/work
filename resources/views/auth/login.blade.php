@extends('layouts.user')

@section('title', 'Login')

@section('content')
<div class="flex items-center justify-center min-h-[calc(100vh-12rem)]">
    <div class="w-full max-w-md">
        
        <!-- Welcome Section -->
        <div class="text-center mb-6 text-black">
            <h1 class="text-2xl font-bold mb-1">WELCOME BACK</h1>
        </div>

        <!-- Login Card -->
        <div class="bg-gradient-to-br from-[#0a2540] via-[#0d3a5f] to-[#1a5080] rounded-3xl shadow-2xl p-6 border border-white border-opacity-10">
            
            <h2 class="text-base font-bold text-white mb-6">Member Login</h2>

            <!-- Login Form -->
            <form method="POST" action="{{ route('login') }}" class="space-y-4" x-data="{ submitting: false }" @submit="submitting = true; showLoading('Signing in...')">
                @csrf

                <!-- Username/Phone -->
                <div>
                    <label for="email" class="block text-white text-xs font-medium mb-2">
                        Username/Phone
                    </label>
                    <input type="text" 
                        id="name" 
                        name="name" 
                        value="{{ old('email') }}"
                        required 
                        autofocus
                        placeholder="Username/Phone"
                        class="block w-full px-3 py-2.5 bg-white bg-opacity-90 border-0 rounded-xl text-gray-900 placeholder-gray-500 text-sm focus:ring-2 focus:ring-orange-500 focus:bg-white transition">
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-white text-xs font-medium mb-2">
                        Password
                    </label>
                    <input type="password" 
                        id="password" 
                        name="password" 
                        required
                        placeholder="Password"
                        class="block w-full px-3 py-2.5 bg-white bg-opacity-90 border-0 rounded-xl text-gray-900 placeholder-gray-500 text-sm focus:ring-2 focus:ring-orange-500 focus:bg-white transition">
                </div>

                <!-- Forgot Password -->
                <div class="text-right">
                    <a @click="showCsPopup = true;" class="text-white text-xs underline hover:text-gray-200 transition">
                        Forgot Password?
                    </a>
                </div>

                <!-- Submit Button -->
                <button type="submit"
                        :disabled="submitting"
                        class="w-full gradient-button text-white py-2.5 px-6 rounded-xl font-bold text-sm shadow-lg disabled:opacity-50 disabled:cursor-not-allowed">
                    <span x-show="!submitting">LOGIN</span>
                    <span x-show="submitting" x-cloak>
                        <i class="fas fa-spinner fa-spin mr-2"></i>loading...
                    </span>
                </button>

                <!-- Create Account Link -->
                <div class="text-center">
                    <p class="text-gray-300 text-xs">
                        Don't have an account?<a href="{{ route(name: 'register') }}" class="text-white underline ml-1 hover:text-gray-200 transition">Create an account</a>
                    </p>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Customer Service Popup Component -->
    <div x-show="showCsPopup" 
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center px-4 py-4"
         style="background: rgba(0, 0, 0, 0.7);">
        
        <div class="bg-white rounded-3xl shadow-2xl max-w-md w-full overflow-hidden"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform translate-y-full"
             x-transition:enter-end="opacity-100 transform translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform translate-y-0"
             x-transition:leave-end="opacity-0 transform translate-y-full">
            
            <!-- Customer Service Options -->
            <div class="divide-y divide-gray-100">
                <!-- Online Customer Service -->
                <a href="#" 
                   class="flex items-center justify-between px-6 py-4 hover:bg-gray-50 transition group">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 rounded-full bg-orange-100 flex items-center justify-center">
                            <i class="fas fa-headset text-orange-500 text-xl"></i>
                        </div>
                        <span class="text-gray-900 font-medium text-base">Online Customer Service</span>
                    </div>
                    <svg class="w-6 h-6 text-gray-400 group-hover:text-orange-500 transition" 
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>

                <!-- Telegram CS -->
                <a href="#" 
                   class="flex items-center justify-between px-6 py-4 hover:bg-gray-50 transition group">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center">
                            <i class="fab fa-telegram-plane text-blue-500 text-xl"></i>
                        </div>
                        <span class="text-gray-900 font-medium text-base">Telegram CS</span>
                    </div>
                    <svg class="w-6 h-6 text-gray-400 group-hover:text-blue-500 transition" 
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            </div>

            <!-- Cancel Button -->
            <div class="p-4 bg-gray-50">
                <button @click="showCsPopup = false" 
                        class="w-full py-2 text-orange-500 font-semibold text-base hover:bg-white rounded-xl transition">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
    function Login() {
        return {
            submitting: false,
            showCsPopup: false,

            onclickShowCsPopup() {

                // Wait a moment for the modal to close animation, then open CS popup
                setTimeout(() => {
                    this.showCsPopup = true;
                }, 2000);
            },

            submitForm() {
                this.submitting = true;
                showLoading('Signing in...');
            }
        };
    }
</script>
    
@endpush