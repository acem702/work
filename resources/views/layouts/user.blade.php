<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - {{ config('app.name') }}</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'de-blue': '#002855',
                        'de-gradient-start': '#002855',
                        'de-gradient-end': '#4CAF50',
                    },
                    fontSize: {
                        'xs': '0.75rem',    // 12px
                        'sm': '0.875rem',   // 14px
                        'base': '1rem',     // 16px
                    }
                }
            }
        }
    </script>
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap');
        
        * {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            font-size: 16px;
        }
        
        body {
            background: linear-gradient(135deg, #e9ecf0ff 0%, rgba(231, 235, 240, 1) 50%, #b9f7bbff 100%);
            min-height: 100vh;
        }
        
        [x-cloak] { display: none !important; }
        
        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.1);
        }
        
        ::-webkit-scrollbar-thumb {
            background: rgba(0, 40, 85, 0.5);
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: rgba(0, 40, 85, 0.7);
        }

        /* Gradient Button */
        .gradient-button {
            background: linear-gradient(135deg, #FF6B6B 0%, #C44569 100%);
            transition: all 0.3s ease;
        }
        
        .gradient-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(255, 107, 107, 0.3);
        }

        /* Desktop Menu Tabs */
        .desktop-menu a {
            position: relative;
            transition: all 0.2s ease;
        }
        
        .desktop-menu a:hover {
            color: #002855;
        }
        
        .desktop-menu a.active::after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 0;
            right: 0;
            height: 3px;
            background: #002855;
        }

        /* Mobile Sidebar */
        .sidebar-menu a {
            transition: all 0.2s ease;
        }
        
        .sidebar-menu a:hover {
            background: rgba(0, 40, 85, 0.05);
            padding-left: 1.5rem;
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <div x-data="{ 
        sidebarOpen: false,
        showAlert: false,
        alertType: 'success',
        alertMessage: '',
        showConfirm: false,
        confirmMessage: '',
        confirmCallback: null,
        showLoading: false,
        loadingMessage: 'Processing...',
        showCsPopup: false
    }" 
    x-init="
        // Listen for custom alert events
        window.addEventListener('show-alert', (e) => {
            alertMessage = e.detail.message;
            alertType = e.detail.type || 'success';
            showAlert = true;
            setTimeout(() => { showAlert = false; }, 4000);
        });
        
        // Listen for loading events
        window.addEventListener('show-loading', (e) => {
            loadingMessage = e.detail.message || 'Processing...';
            showLoading = true;
        });
        
        window.addEventListener('hide-loading', () => {
            showLoading = false;
        });
        
        // Check for Laravel session messages
        @if(session('success'))
            setTimeout(() => {
                window.dispatchEvent(new CustomEvent('show-alert', {
                    detail: { message: '{{ session('success') }}', type: 'success' }
                }));
            }, 100);
        @endif
        
        @if(session('error'))
            setTimeout(() => {
                window.dispatchEvent(new CustomEvent('show-alert', {
                    detail: { message: '{{ session('error') }}', type: 'error' }
                }));
            }, 100);
        @endif
        
        @if($errors->any())
            setTimeout(() => {
                window.dispatchEvent(new CustomEvent('show-alert', {
                    detail: { message: '{{ $errors->first() }}', type: 'error' }
                }));
            }, 100);
        @endif
    "
    @keydown.escape="sidebarOpen = false">
        
        <!-- Header -->
        <header class="fixed top-0 left-0 right-0 z-40 bg-white shadow-md">
            <div class="max-w-7xl mx-auto px-4 py-2">
                <div class="flex items-center justify-between">
                    
                    <!-- Mobile Menu Button (visible only on mobile) -->
                    <button @click="sidebarOpen = true" 
                            class="lg:hidden text-gray-900 p-2 focus:outline-none">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>

                    <!-- Logo -->
                    <div class="flex-shrink-0">
                        <img src="{{ asset('logo.svg') }}" 
                             alt="The Digital Elevator" 
                             class="h-8">
                    </div>

                    <!-- Desktop Navigation Tabs (hidden on mobile) -->
                    <nav class="hidden lg:flex items-center space-x-6 desktop-menu text-sm">
                        <a href="#" class="text-gray-600 hover:text-gray-900 font-medium py-2">SHOES</a>
                        <a href="#" class="text-gray-600 hover:text-gray-900 font-medium py-2">APPAREL</a>
                        <a href="#" class="text-gray-600 hover:text-gray-900 font-medium py-2">ELECTRONICS</a>
                        <a href="#" class="text-gray-600 hover:text-gray-900 font-medium py-2">ACCESSORIES</a>
                        <a href="#" class="text-gray-600 hover:text-gray-900 font-medium py-2">JEWELRIES</a>
                        <a href="#" class="text-gray-600 hover:text-gray-900 font-medium py-2">WATCHES</a>
                        <a href="#" class="text-gray-600 hover:text-gray-900 font-medium py-2">FURNITURES</a>
                        <a href="#" class="text-gray-600 hover:text-gray-900 font-medium py-2">MEDICINE</a>
                    </nav>

                    <!-- Right Side Actions -->
                    <div class="flex items-center space-x-3">
                        <!-- Desktop Account Menu -->
                        <div class="hidden lg:flex items-center space-x-4 text-sm">
                            <a href="{{ route(name: 'account.index') }}" class="text-gray-600 hover:text-gray-900 font-medium {{ request()->routeIs('dashboard') ? 'text-gray-900' : '' }}">
                                <i class="fas fa-user mr-1"></i> MY ACCOUNT
                            </a>
                            <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-gray-900 font-medium {{ request()->routeIs('tasks.*') ? 'text-gray-900' : '' }}">
                                DASHBOARD
                            </a>
                            <a href="{{ route('membership.index') }}" class="text-gray-600 hover:text-gray-900 font-medium {{ request()->routeIs('membership.*') ? 'text-gray-900' : '' }}">
                                MEMBERSHIP
                            </a>
                        </div>

                        <!-- Logout Button -->
                        <button @click="showConfirm = true; confirmMessage = 'Are you sure you want to logout?'; confirmCallback = () => document.getElementById('logout-form').submit();" 
                                class="text-gray-900 p-2 focus:outline-none hover:text-gray-600 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                        </button>
                    </div>

                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                        @csrf
                    </form>
                </div>
            </div>
        </header>

        <!-- Mobile Sidebar Overlay -->
        <div x-show="sidebarOpen" 
             x-cloak
             @click="sidebarOpen = false"
             class="fixed inset-0 z-40 bg-black bg-opacity-50 lg:hidden"
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
        </div>

        <!-- Mobile Sidebar -->
        <aside class="fixed inset-y-0 left-0 z-50 w-72 bg-white transform transition-transform duration-300 ease-in-out shadow-2xl lg:hidden"
               :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
            
            <div class="flex flex-col h-full">
                
                <!-- Close Button -->
                <div class="flex justify-end p-4">
                    <button @click="sidebarOpen = false" class="text-gray-600 hover:text-gray-900">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Brand -->
                <div class="px-6 mb-6">
                    <h2 class="text-lg font-bold text-gray-900">Digital Elevator</h2>
                </div>

                <!-- Navigation Menu -->
                <nav class="flex-1 px-4 space-y-1 overflow-y-auto sidebar-menu">
                    
                    <!-- Product Categories -->
                    <a href="#" class="block px-4 py-2.5 text-gray-700 hover:bg-gray-100 rounded-lg text-sm font-medium">
                        SHOES
                    </a>
                    <a href="#" class="block px-4 py-2.5 text-gray-700 hover:bg-gray-100 rounded-lg text-sm font-medium">
                        APPAREL
                    </a>
                    <a href="#" class="block px-4 py-2.5 text-gray-700 hover:bg-gray-100 rounded-lg text-sm font-medium">
                        ELECTRONICS
                    </a>
                    <a href="#" class="block px-4 py-2.5 text-gray-700 hover:bg-gray-100 rounded-lg text-sm font-medium">
                        ACCESSORIES
                    </a>
                    <a href="#" class="block px-4 py-2.5 text-gray-700 hover:bg-gray-100 rounded-lg text-sm font-medium">
                        JEWELRIES
                    </a>
                    <a href="#" class="block px-4 py-2.5 text-gray-700 hover:bg-gray-100 rounded-lg text-sm font-medium">
                        WATCHES
                    </a>
                    <a href="#" class="block px-4 py-2.5 text-gray-700 hover:bg-gray-100 rounded-lg text-sm font-medium">
                        FURNITURES
                    </a>
                    <a href="#" class="block px-4 py-2.5 text-gray-700 hover:bg-gray-100 rounded-lg text-sm font-medium">
                        MEDICINE
                    </a>
                    
                    <!-- Divider -->
                    <div class="border-t border-gray-200 my-4"></div>
                    
                    <!-- Account Links -->
                    <a href="{{ route(name: 'account.index') }}" class="flex items-center px-4 py-2.5 text-gray-700 hover:bg-gray-100 rounded-lg text-sm font-medium {{ request()->routeIs('dashboard') ? 'bg-gray-100' : '' }}">
                        <i class="fas fa-user w-4 mr-2 text-xs"></i>
                        MY ACCOUNT
                    </a>
                    
                    <a href="{{ route('dashboard') }}" class="block px-4 py-2.5 text-gray-700 hover:bg-gray-100 rounded-lg text-sm font-medium {{ request()->routeIs('tasks.*') ? 'bg-gray-100' : '' }}">
                        DASHBOARD
                    </a>
                    
                    <a href="{{ route('membership.index') }}" class="block px-4 py-2.5 text-gray-700 hover:bg-gray-100 rounded-lg text-sm font-medium {{ request()->routeIs('membership.*') ? 'bg-gray-100' : '' }}">
                        PREMIUM MEMBERSHIP
                    </a>
                </nav>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="pt-14 pb-8 min-h-screen">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                @yield('content')
            </div>
        </main>

        <!-- Footer (Not Fixed) -->
        <footer class="bg-[#1a1a2e] text-white mt-12">
            <div class="max-w-7xl mx-auto px-4 py-8">
                
                <!-- Footer Logo - Left Aligned -->
                <div class="mb-6">
                    <img src="{{ asset('logo-white.svg') }}" 
                         alt="The Digital Elevator" 
                         class="h-12">
                </div>

                <!-- Footer Description -->
                <div class="mb-6">
                    <p class="text-gray-300 text-xs leading-relaxed max-w-3xl">
                        We are the only life sciences marketing agency that leverages 10+ years of proprietary data and insights, combined with 70+ in-house Ph.D. scientist-marketers.
                    </p>
                </div>

                <div class="mb-6">
                    <p class="text-gray-300 text-xs">
                        Join over 4,000 life science marketers who receive weekly digital marketing tips.
                    </p>
                </div>

                <!-- Footer Links -->
                <div class="grid grid-cols-2 gap-8 mb-6">
                    
                    <!-- Company -->
                    <div>
                        <h3 class="text-white font-semibold text-xs mb-3">COMPANY</h3>
                        <ul class="space-y-1 text-xs">
                            <li><a href="#" class="text-gray-300 hover:text-white text-xs">About Us</a></li>
                            <li><a href="#" class="text-gray-300 hover:text-white text-xs">Contact Us</a></li>
                            <li><a href="{{ route('membership.index') }}" class="text-gray-300 hover:text-white text-xs">Premium Membership</a></li>
                            <li><a href="#" class="text-gray-300 hover:text-white text-xs">Company Notice</a></li>
                        </ul>
                    </div>

                    <!-- Information -->
                    <div>
                        <h3 class="text-white font-semibold text-xs mb-3">INFORMATION</h3>
                        <ul class="space-y-1 text-xs">
                            <li><a href="#" class="text-gray-300 hover:text-white text-xs">Privacy Policy</a></li>
                            <li><a href="#" class="text-gray-300 hover:text-white text-xs">Terms and Conditions</a></li>
                            <li><a href="#" class="text-gray-300 hover:text-white text-xs">FAQs</a></li>
                            <li><a href="#" class="text-gray-300 hover:text-white text-xs">Latest Events</a></li>
                            <li><a href="#" class="text-gray-300 hover:text-white text-xs">Acceptable Use Policy</a></li>
                        </ul>
                    </div>
                </div>

                <!-- Copyright -->
                <div class="pt-6 border-t border-gray-700">
                    <p class="text-gray-400 text-xs">
                        © {{ date('Y') }}, by Digital Elevator | Company Number : P09000003729
                    </p>
                </div>
            </div>
        </footer>

        <!-- Floating Chat Button -->
        <div class="fixed bottom-6 right-4 z-50">
            <button @click="showCsPopup = true;" 
                    class="w-12 h-12 bg-gradient-to-br from-orange-400 to-orange-600 rounded-full shadow-2xl flex items-center justify-center text-white hover:scale-110 transition-transform">
                <img src="{{ asset('logo-white.svg') }}" 
                     alt="Chat" 
                     class="w-6 h-6">
            </button>
        </div>

        <!-- Custom Alert Toast -->
        <div x-show="showAlert" 
             x-cloak
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform translate-y-2"
             x-transition:enter-end="opacity-100 transform translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform translate-y-0"
             x-transition:leave-end="opacity-0 transform translate-y-2"
             class="fixed top-20 left-1/2 transform -translate-x-1/2 z-50 w-11/12 max-w-sm">
            <div class="rounded-xl shadow-2xl p-3 text-center text-sm"
                 :class="{
                     'bg-red-600': alertType === 'error',
                     'bg-green-600': alertType === 'success',
                     'bg-yellow-600': alertType === 'warning',
                     'bg-blue-600': alertType === 'info'
                 }">
                <p class="text-white font-medium" x-text="alertMessage"></p>
            </div>
        </div>

        <!-- Custom Confirm Dialog -->
        <div x-show="showConfirm" 
             x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center px-4"
             style="background: rgba(0, 0, 0, 0.6);">
            <div class="bg-white rounded-2xl shadow-2xl max-w-sm w-full p-6 text-center"
                 @click.away="showConfirm = false"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform scale-90"
                 x-transition:enter-end="opacity-100 transform scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 transform scale-100"
                 x-transition:leave-end="opacity-0 transform scale-90">
                
                <h3 class="text-base font-bold text-gray-900 mb-3">Logout</h3>
                <p class="text-gray-600 text-sm mb-6" x-text="confirmMessage"></p>
                
                <div class="flex space-x-3">
                    <button @click="showConfirm = false" 
                            class="flex-1 px-4 py-2 border-2 border-gray-300 text-gray-700 rounded-xl font-semibold text-sm hover:bg-gray-50 transition">
                        Cancel
                    </button>
                    <button @click="if(confirmCallback) confirmCallback(); showConfirm = false;" 
                            class="flex-1 px-4 py-2 bg-gradient-to-r from-orange-500 to-red-500 text-white rounded-xl font-semibold text-sm hover:shadow-lg transition">
                        Confirm
                    </button>
                </div>
            </div>
        </div>

        <!-- Loading Indicator -->
        <div x-show="showLoading" 
            x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center"
            style="background: rgba(0, 0, 0, 0.7);">
            <div class="bg-white rounded-2xl shadow-2xl p-8 text-center max-w-sm mx-4">
                <!-- Spinner -->
                <div class="w-16 h-16 mx-auto mb-4">
                    <svg class="animate-spin h-16 w-16 text-orange-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
                <p class="text-gray-900 font-semibold text-base" x-text="loadingMessage"></p>
            </div>
        </div>
        @include('partials.customer-service-popup')
    </div>


    <!-- Alpine.js - Load AFTER store definition -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <script>
        // Setup CSRF token for axios
        axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').content;
        
        // Global alert function
        window.showAlert = function(message, type = 'success') {
            window.dispatchEvent(new CustomEvent('show-alert', {
                detail: { message, type }
            }));
        };

        // Global loading functions
        window.showLoading = function(message = 'Processing...') {
            window.dispatchEvent(new CustomEvent('show-loading', {
                detail: { message }
            }));
        };

        window.hideLoading = function() {
            window.dispatchEvent(new Event('hide-loading'));
        };

        // Global confirm function
        window.showConfirm = function(message, callback) {
            // This needs to access Alpine data
            document.dispatchEvent(new CustomEvent('confirm-dialog', {
                detail: { message, callback }
            }));
        };

        window.showCsPopup = function() {
            // This needs to access Alpine data
            setTimeout(() => {
                document.dispatchEvent(new Event('show-cs-popup'));
            }, 5000);
        };

    </script>

    @stack('scripts')
</body>
</html>