@extends('layouts.app')

@section('title', 'Help Center - ' . ($category['title'] ?? 'Category'))

@section('content')

<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <a href="{{ route('self-service.index') }}" class="inline-flex items-center text-sm text-blue-600 hover:text-blue-700 mb-6">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            All help topics
        </a>

        <div class="bg-white rounded-xl shadow p-6 mb-6">
            <div class="flex items-center mb-2">
                <span class="text-3xl mr-3">{{ $category['icon'] ?? '🛈' }}</span>
                <h1 class="text-2xl font-bold text-gray-900">{{ $category['title'] ?? ucfirst($categorySlug) }}</h1>
            </div>
            <p class="text-gray-600">{{ $category['description'] ?? '' }}</p>
        </div>

        <div class="bg-white rounded-xl shadow p-6">
            <div class="text-sm font-semibold text-gray-700 mb-4">Quick actions</div>

            @if($categorySlug === 'orders')
                <div class="space-y-3">
                    <button onclick="quickAction('track_order', '{{ $categorySlug }}')" class="w-full text-left px-4 py-3 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition">
                        Track My Order
                    </button>
                    <button onclick="quickAction('modify_order', '{{ $categorySlug }}')" class="w-full text-left px-4 py-3 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition">
                        Cancel/Modify Order
                    </button>
                </div>
            @elseif($categorySlug === 'products')
                <div class="space-y-3">
                    <button onclick="quickAction('verify_authenticity', '{{ $categorySlug }}')" class="w-full text-left px-4 py-3 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition">
                        Verify Authenticity
                    </button>
                    <button onclick="quickAction('care_instructions', '{{ $categorySlug }}')" class="w-full text-left px-4 py-3 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition">
                        Care Instructions
                    </button>
                </div>
            @elseif($categorySlug === 'returns')
                <div class="space-y-3">
                    <button onclick="quickAction('start_return', '{{ $categorySlug }}')" class="w-full text-left px-4 py-3 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition">
                        Start a Return
                    </button>
                    <button onclick="quickAction('refund_status', '{{ $categorySlug }}')" class="w-full text-left px-4 py-3 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition">
                        Check Refund Status
                    </button>
                </div>
            @elseif($categorySlug === 'account')
                <div class="space-y-3">
                    <button onclick="quickAction('reset_password', '{{ $categorySlug }}')" class="w-full text-left px-4 py-3 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition">
                        Reset Password
                    </button>
                    <button onclick="quickAction('update_profile', '{{ $categorySlug }}')" class="w-full text-left px-4 py-3 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition">
                        Update Profile
                    </button>
                </div>
            @endif
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
            <button onclick="submitQuickAction()" class="flex-1 bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition">
                Get Help
            </button>
            <button onclick="escalateToChat()" class="flex-1 bg-gray-200 text-gray-800 py-2 px-4 rounded-lg hover:bg-gray-300 transition">
                Chat with Agent
            </button>
        </div>
    </div>
</div>

<script>
let currentAction = null;
let currentCategory = @json($categorySlug);

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
    } else {
        formHTML = `
            <div class="text-center">
                <p class="text-gray-600 mb-4">We'll help you with ${actionTitles[action]?.toLowerCase() || 'this'}.</p>
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
        window.location.href = '/chat/conversations';
    });
}
</script>

@endsection

