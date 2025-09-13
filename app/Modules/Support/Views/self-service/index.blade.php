@extends('layouts.app')

@section('title', 'Help Center - Jewelry Store')

@section('content')

<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">How can we help you?</h1>
            <p class="text-xl text-gray-600">Find quick solutions or get personalized support</p>
        </div>

        <!-- Quick Search -->
        <div class="mb-12">
            <div class="max-w-2xl mx-auto">
                <div class="relative">
                    <input 
                        type="text" 
                        id="helpSearch" 
                        placeholder="Describe your issue (e.g., 'track my order', 'return item')" 
                        class="w-full px-6 py-4 text-lg border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm"
                    >
                    <button 
                        onclick="searchHelp()"
                        class="absolute right-2 top-2 bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition duration-150"
                    >
                        Search
                    </button>
                </div>
                <div id="searchResults" class="mt-4 hidden"></div>
            </div>
        </div>

        <!-- Self-Service Categories -->
        <div class="grid md:grid-cols-2 gap-6 mb-12">
            @foreach($categories as $slug => $category)
                <div class="bg-white rounded-xl shadow-md hover:shadow-lg transition duration-300 cursor-pointer" onclick="selectCategory('{{ $slug }}')">
                    <div class="p-8">
                        <div class="flex items-center mb-4">
                            <span class="text-4xl mr-4">{{ $category['icon'] }}</span>
                            <h3 class="text-2xl font-bold text-gray-900">{{ $category['title'] }}</h3>
                        </div>
                        <p class="text-gray-600 mb-6">{{ $category['description'] }}</p>
                        
                        <!-- Quick Actions for this category -->
                        <div class="space-y-2">
                            <div class="text-sm font-medium text-gray-500 mb-3">Quick actions:</div>
                            @if($slug === 'orders')
                                <button onclick="quickAction('track_order', '{{ $slug }}')" class="block w-full text-left px-4 py-2 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition">
                                    📦 Track My Order
                                </button>
                                <button onclick="quickAction('modify_order', '{{ $slug }}')" class="block w-full text-left px-4 py-2 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition">
                                    ✏️ Cancel/Modify Order
                                </button>
                            @elseif($slug === 'products')
                                <button onclick="quickAction('verify_authenticity', '{{ $slug }}')" class="block w-full text-left px-4 py-2 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition">
                                    ✅ Verify Authenticity
                                </button>
                                <button onclick="quickAction('care_instructions', '{{ $slug }}')" class="block w-full text-left px-4 py-2 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition">
                                    🧽 Care Instructions
                                </button>
                            @elseif($slug === 'returns')
                                <button onclick="quickAction('start_return', '{{ $slug }}')" class="block w-full text-left px-4 py-2 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition">
                                    📤 Start a Return
                                </button>
                                <button onclick="quickAction('refund_status', '{{ $slug }}')" class="block w-full text-left px-4 py-2 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition">
                                    💰 Check Refund Status
                                </button>
                            @elseif($slug === 'account')
                                <button onclick="quickAction('reset_password', '{{ $slug }}')" class="block w-full text-left px-4 py-2 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition">
                                    🔑 Reset Password
                                </button>
                                <button onclick="quickAction('update_profile', '{{ $slug }}')" class="block w-full text-left px-4 py-2 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition">
                                    👤 Update Profile
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Still Need Help Section -->
        <div class="bg-gradient-to-r from-purple-600 to-blue-600 rounded-xl shadow-lg text-white p-8 text-center">
            <h2 class="text-2xl font-bold mb-4">Still need help?</h2>
            <p class="text-lg mb-6 opacity-90">Our customer service team is ready to assist you personally</p>
            
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <button 
                    onclick="startLiveChat()" 
                    class="bg-white text-purple-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition duration-150 flex items-center justify-center"
                >
                    💬 Start Live Chat
                </button>
                <a 
                    href="mailto:support@jewelrystore.com" 
                    class="bg-transparent border-2 border-white text-white px-8 py-3 rounded-lg font-semibold hover:bg-white hover:text-purple-600 transition duration-150 flex items-center justify-center"
                >
                    ✉️ Email Support
                </a>
                <a 
                    href="tel:1-800-JEWELRY" 
                    class="bg-transparent border-2 border-white text-white px-8 py-3 rounded-lg font-semibold hover:bg-white hover:text-purple-600 transition duration-150 flex items-center justify-center"
                >
                    📞 Call Us
                </a>
            </div>
        </div>

    </div>
</div>

<!-- Quick Action Modal -->
<div id="quickActionModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl max-w-md w-full p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 id="modalTitle" class="text-xl font-bold text-gray-900"></h3>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <div id="modalContent"></div>
        
        <div class="mt-6 flex gap-3">
            <button 
                onclick="submitQuickAction()" 
                class="flex-1 bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition"
            >
                Get Help
            </button>
            <button 
                onclick="escalateToChat()" 
                class="flex-1 bg-gray-200 text-gray-800 py-2 px-4 rounded-lg hover:bg-gray-300 transition"
            >
                Chat with Agent
            </button>
        </div>
    </div>
</div>

<script>
let currentAction = null;
let currentCategory = null;

function selectCategory(slug) {
    window.location.href = `/self-service/${slug}`;
}

function quickAction(action, category) {
    currentAction = action;
    currentCategory = category;
    
    const modal = document.getElementById('quickActionModal');
    const title = document.getElementById('modalTitle');
    const content = document.getElementById('modalContent');
    
    const actionTitles = {
        'track_order': 'Track Your Order',
        'modify_order': 'Modify Your Order',
        'verify_authenticity': 'Verify Authenticity',
        'care_instructions': 'Care Instructions',
        'start_return': 'Start a Return',
        'refund_status': 'Check Refund Status',
        'reset_password': 'Reset Password',
        'update_profile': 'Update Profile'
    };
    
    title.textContent = actionTitles[action] || 'Quick Help';
    
    // Create form based on action
    let formHTML = '';
    if (action === 'track_order') {
        formHTML = `
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Order Number or Tracking Code</label>
                    <input type="text" id="order_number" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="e.g., 1024 or JW12345" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                    <input type="email" id="email" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="your@email.com" />
                </div>
                <div id="trackingResults" class="mt-4 hidden"></div>
            </div>
        `;
    } else if (action === 'reset_password') {
        formHTML = `
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                    <input type="email" id="reset_email" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="your@email.com">
                </div>
            </div>
        `;
    } else {
        formHTML = `
            <div class="text-center">
                <p class="text-gray-600 mb-4">We'll help you with ${actionTitles[action].toLowerCase()}. Would you like to:</p>
                <div class="space-y-2">
                    <button onclick="submitQuickAction()" class="w-full bg-blue-50 text-blue-700 p-3 rounded-lg hover:bg-blue-100">Get instant help</button>
                    <button onclick="escalateToChat()" class="w-full bg-gray-50 text-gray-700 p-3 rounded-lg hover:bg-gray-100">Chat with an agent</button>
                </div>
            </div>
        `;
    }
    
    content.innerHTML = formHTML;
    modal.classList.remove('hidden');
}

function closeModal() {
    document.getElementById('quickActionModal').classList.add('hidden');
}

function submitQuickAction() {
    if (currentAction === 'track_order') {
        const orderNumber = document.getElementById('order_number')?.value?.trim();
        const email = document.getElementById('email')?.value?.trim();

        if (!orderNumber || !email) {
            alert('Please enter both order number and email.');
            return;
        }

        const results = document.getElementById('trackingResults');
        if (results) {
            results.classList.remove('hidden');
            results.innerHTML = '<div class="text-sm text-gray-600">Looking up your order...</div>';
        }

        const headers = {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        };

        const payload = JSON.stringify({ order_number: orderNumber, email });

        // Try API path first, then fall back to internal
        fetch('/self-service/track-order?use_api=1', { method: 'POST', headers, body: payload })
            .then(async (res) => {
                if (!res.ok) throw new Error('API tracking failed');
                return res.json();
            })
            .catch(() => fetch('/self-service/track-order', { method: 'POST', headers, body: payload }).then(r => r.json()))
            .then(data => {
                if (!data || !data.success) {
                    throw new Error(data?.message || 'Unable to track order');
                }

                const t = data.data || {};
                const html = `
                    <div class="p-3 rounded-lg bg-gray-50 border border-gray-200">
                        <div class="text-sm text-gray-700 mb-1"><strong>Order #</strong>: ${t.order_id ?? '—'}</div>
                        <div class="text-sm text-gray-700 mb-1"><strong>Status</strong>: ${t.status ?? '—'}</div>
                        <div class="text-sm text-gray-700 mb-1"><strong>Tracking</strong>: ${t.tracking_number ?? '—'}</div>
                        <div class="text-sm text-gray-700 mb-1"><strong>Shipping</strong>: ${t.shipping_method ?? '—'}</div>
                        <div class="text-xs text-gray-500 mt-2">Updated: ${t.updated_at ?? '—'}</div>
                    </div>
                `;
                if (results) results.innerHTML = html;
            })
            .catch(err => {
                console.error(err);
                if (results) {
                    results.innerHTML = '<div class="text-sm text-red-600">Could not find your order. Please confirm the details or chat with an agent.</div>';
                }
            });
        return;
    }

    // Default: self-service help flow
    fetch('/self-service/help', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            issue: currentAction,
            category: currentCategory
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.solution);
            closeModal();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        escalateToChat();
    });
}

function escalateToChat() {
    fetch('/self-service/escalate', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            issue: currentAction,
            category: currentCategory,
            reason: 'Escalated from self-service'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.redirect) {
            window.location.href = data.redirect;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        // Fallback to direct chat
        startLiveChat();
    });
}

function startLiveChat() {
    // Set escalation context and redirect to chat
    fetch('/self-service/escalate', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            category: 'general',
            reason: 'Direct chat request from self-service'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.redirect) {
            window.location.href = data.redirect;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        // Fallback redirect
        window.location.href = '/chat/conversations';
    });
}

function searchHelp() {
    const query = document.getElementById('helpSearch').value;
    if (query.trim() === '') return;
    
    // Simple keyword matching for demo
    const keywords = {
        'track': 'orders',
        'order': 'orders', 
        'payment': 'orders',
        'return': 'returns',
        'refund': 'returns',
        'exchange': 'returns',
        'account': 'account',
        'password': 'account',
        'profile': 'account',
        'authentic': 'products',
        'care': 'products',
        'quality': 'products'
    };
    
    const lowerQuery = query.toLowerCase();
    let matchedCategory = null;
    
    for (const [keyword, category] of Object.entries(keywords)) {
        if (lowerQuery.includes(keyword)) {
            matchedCategory = category;
            break;
        }
    }
    
    if (matchedCategory) {
        selectCategory(matchedCategory);
    } else {
        // Show general help or escalate to chat
        alert('Let me connect you with a support agent who can help with: ' + query);
        startLiveChat();
    }
}
</script>

@endsection
