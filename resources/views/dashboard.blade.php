<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Welcome to ') }}{{ config('app.name', 'Jewelry Store') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Welcome Banner -->
            <div class="bg-gradient-to-r from-blue-600 to-purple-600 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-8 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-3xl font-bold mb-2">Welcome back, {{ Auth::user()->name }}! 👋</h1>
                            <p class="text-blue-100">Discover our exquisite jewelry collection and get support when you need it.</p>
                        </div>
                        <div class="hidden md:block">
                            <svg class="w-24 h-24 text-blue-200" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Navigation Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                
                <!-- Products Card -->
                <a href="{{ route('products.index') }}" class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-lg transition-shadow duration-300 transform hover:scale-105">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                                    <span class="text-2xl">💎</span>
                                </div>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold text-gray-900">Products</h3>
                                <p class="text-sm text-gray-600">Browse our jewelry collection</p>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="text-sm text-purple-600 font-medium">View Catalog →</div>
                        </div>
                    </div>
                </a>

                <!-- Support Tickets Card -->
                <a href="{{ route('tickets.index') }}" class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-lg transition-shadow duration-300 transform hover:scale-105">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                                    <span class="text-2xl">🎫</span>
                                </div>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold text-gray-900">Support Tickets</h3>
                                <p class="text-sm text-gray-600">Track your support requests</p>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="text-sm text-green-600 font-medium">Manage Tickets →</div>
                        </div>
                    </div>
                </a>

                <!-- FAQ & Help Card -->
                <a href="{{ route('faq.index') }}" class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-lg transition-shadow duration-300 transform hover:scale-105">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <span class="text-2xl">📚</span>
                                </div>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold text-gray-900">FAQ & Help</h3>
                                <p class="text-sm text-gray-600">Find quick answers</p>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="text-sm text-blue-600 font-medium">Get Help →</div>
                        </div>
                    </div>
                </a>

                <!-- Live Chat Card -->
                <div onclick="startLiveChat()" class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-lg transition-shadow duration-300 transform hover:scale-105 cursor-pointer">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                                    <span class="text-2xl">💬</span>
                                </div>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold text-gray-900">Live Chat</h3>
                                <p class="text-sm text-gray-600">Chat with our team</p>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="text-sm text-orange-600 font-medium">Start Chat →</div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Support Journey Section -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Need Help? We've Got You Covered! 🛠️</h3>
                    <p class="text-gray-600 mb-6">Follow our support journey for the fastest resolution:</p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="text-center p-4 bg-gray-50 rounded-lg">
                            <div class="text-3xl mb-2">📚</div>
                            <h4 class="font-semibold text-gray-900">1. FAQ & Help</h4>
                            <p class="text-sm text-gray-600 mt-1">Quick answers to common questions</p>
                            <a href="{{ route('faq.index') }}" class="text-blue-600 text-sm font-medium mt-2 inline-block">Start Here →</a>
                        </div>
                        
                        <div class="text-center p-4 bg-gray-50 rounded-lg">
                            <div class="text-3xl mb-2">🛠️</div>
                            <h4 class="font-semibold text-gray-900">2. Self Service</h4>
                            <p class="text-sm text-gray-600 mt-1">Interactive troubleshooting</p>
                            <a href="{{ route('self-service.index') }}" class="text-green-600 text-sm font-medium mt-2 inline-block">Try This →</a>
                        </div>
                        
                        <div class="text-center p-4 bg-gray-50 rounded-lg">
                            <div class="text-3xl mb-2">🎫</div>
                            <h4 class="font-semibold text-gray-900">3. Support Ticket</h4>
                            <p class="text-sm text-gray-600 mt-1">Detailed issue tracking</p>
                            <a href="{{ route('tickets.create') }}" class="text-purple-600 text-sm font-medium mt-2 inline-block">Create Ticket →</a>
                        </div>
                        
                        <div class="text-center p-4 bg-gray-50 rounded-lg">
                            <div class="text-3xl mb-2">💬</div>
                            <h4 class="font-semibold text-gray-900">4. Live Chat</h4>
                            <p class="text-sm text-gray-600 mt-1">Real-time assistance</p>
                            <button onclick="startLiveChat()" class="text-orange-600 text-sm font-medium mt-2 inline-block">Chat Now →</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                
                <!-- Account Actions -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Account & Profile 👤</h3>
                        <div class="space-y-3">
                            <a href="{{ route('profile.edit') }}" class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center mr-3">
                                    <span class="text-sm">⚙️</span>
                                </div>
                                <div>
                                    <div class="font-medium text-gray-900">Edit Profile</div>
                                    <div class="text-sm text-gray-600">Update your account information</div>
                                </div>
                            </a>
                            
                            <a href="{{ route('tickets.index') }}" class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                                    <span class="text-sm">📋</span>
                                </div>
                                <div>
                                    <div class="font-medium text-gray-900">My Support Tickets</div>
                                    <div class="text-sm text-gray-600">View your support history</div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Browse & Shop -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Browse & Shop 🛍️</h3>
                        <div class="space-y-3">
                            <a href="{{ route('products.index') }}" class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                                    <span class="text-sm">💎</span>
                                </div>
                                <div>
                                    <div class="font-medium text-gray-900">View All Products</div>
                                    <div class="text-sm text-gray-600">Browse our jewelry collection</div>
                                </div>
                            </a>
                            
                            <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                                <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center mr-3">
                                    <span class="text-sm">🛒</span>
                                </div>
                                <div>
                                    <div class="font-medium text-gray-500">Shopping Cart</div>
                                    <div class="text-sm text-gray-400">Coming soon...</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>

    <!-- Include Chat Widget -->
    <x-chat-widget />
    
</x-app-layout>
