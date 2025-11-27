<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CS Popup Test</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gray-100 p-8">
    
    <div class="max-w-2xl mx-auto space-y-4">
        <h1 class="text-2xl font-bold mb-4">Customer Service Popup Test</h1>
        
        <!-- Test Button -->
        <button @click="$store.csPopup.open()" 
                class="px-6 py-3 bg-orange-500 text-white rounded-lg font-bold hover:bg-orange-600">
            Test Open Popup
        </button>

        <!-- Debug Info -->
        <div class="bg-white p-4 rounded-lg shadow">
            <h2 class="font-bold mb-2">Debug Info:</h2>
            <div class="space-y-2 text-sm">
                <div>
                    <strong>Alpine Loaded:</strong> 
                    <span x-data x-text="'Yes'">Checking...</span>
                </div>
                <div x-data="{ storeExists: false }" x-init="storeExists = typeof $store.csPopup !== 'undefined'">
                    <strong>Store Exists:</strong> 
                    <span x-text="storeExists ? 'Yes ✓' : 'No ✗'"></span>
                </div>
                <div x-data x-init="console.log('Alpine Store:', $store.csPopup)">
                    <strong>Check Console:</strong> Look for "Alpine Store:" message
                </div>
            </div>
        </div>

        <!-- Manual Test -->
        <div class="bg-white p-4 rounded-lg shadow">
            <h2 class="font-bold mb-2">Manual Tests:</h2>
            <div class="space-y-2">
                <button onclick="testAlpine()" 
                        class="px-4 py-2 bg-blue-500 text-white rounded">
                    1. Test Alpine in Console
                </button>
                <button onclick="testStore()" 
                        class="px-4 py-2 bg-green-500 text-white rounded">
                    2. Test Store in Console
                </button>
                <button onclick="testOpen()" 
                        class="px-4 py-2 bg-purple-500 text-white rounded">
                    3. Manually Open Popup
                </button>
            </div>
        </div>
    </div>

    <!-- Customer Service Popup Component -->
    <div x-data="{ isOpen: false }" x-show="$store.csPopup?.isOpen || isOpen" 
         x-cloak
         class="fixed inset-0 z-50 flex items-end justify-center px-4 pb-4"
         style="background: rgba(0, 0, 0, 0.4);"
         @click.self="$store.csPopup?.close(); isOpen = false">
        
        <div class="bg-white rounded-t-3xl shadow-2xl max-w-md w-full overflow-hidden"
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
                <button @click="$store.csPopup?.close(); isOpen = false" 
                        class="w-full py-3 text-orange-500 font-semibold text-base hover:bg-white rounded-xl transition">
                    Cancel
                </button>
            </div>
        </div>
    </div>

    <!-- CRITICAL: Store MUST be defined BEFORE Alpine loads -->
    <script>
        document.addEventListener('alpine:init', () => {
            console.log('Alpine initializing...');
            Alpine.store('csPopup', {
                isOpen: false,
                
                open() {
                    console.log('Opening CS Popup');
                    this.isOpen = true;
                },
                
                close() {
                    console.log('Closing CS Popup');
                    this.isOpen = false;
                },
                
                toggle() {
                    console.log('Toggling CS Popup');
                    this.isOpen = !this.isOpen;
                }
            });
            console.log('CS Popup store registered');
        });

        // Test functions
        function testAlpine() {
            console.log('Alpine object:', window.Alpine);
            if (window.Alpine) {
                alert('✓ Alpine is loaded!');
            } else {
                alert('✗ Alpine is NOT loaded!');
            }
        }

        function testStore() {
            if (window.Alpine && window.Alpine.store('csPopup')) {
                console.log('Store contents:', window.Alpine.store('csPopup'));
                alert('✓ Store exists! Check console for details.');
            } else {
                alert('✗ Store does NOT exist!');
            }
        }

        function testOpen() {
            if (window.Alpine && window.Alpine.store('csPopup')) {
                window.Alpine.store('csPopup').open();
                alert('Popup open command sent!');
            } else {
                alert('Cannot open - store not found!');
            }
        }
    </script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

</body>
</html>