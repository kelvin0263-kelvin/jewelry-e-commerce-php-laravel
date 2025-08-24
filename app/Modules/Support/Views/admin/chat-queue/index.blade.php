@extends('layouts.admin')

@section('title', 'Chat Queue Management')

@push('styles')
<style>
/* Main Container Styling */
.chat-queue-container {
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    min-height: 100vh;
    padding: 2rem;
}

/* Header Section */
.queue-header {
    background: white;
    border-radius: 16px;
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    border: 1px solid rgba(0, 0, 0, 0.05);
}

.queue-header h1 {
    color: #1e293b;
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.queue-header p {
    color: #64748b;
    font-size: 1.1rem;
}

/* Stats Cards */
.stats-container {
    margin-bottom: 2rem;
}

.stat-card {
    background: white;
    border-radius: 16px;
    padding: 2rem;
    border: 1px solid rgba(0, 0, 0, 0.05);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    transition: all 0.3s ease;
    height: 100%;
}

.stat-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    margin-bottom: 1rem;
}

.stat-icon.warning {
    background: linear-gradient(135deg, #fef3c7, #fbbf24);
    color: #92400e;
}

.stat-icon.success {
    background: linear-gradient(135deg, #d1fae5, #10b981);
    color: #065f46;
}

.stat-icon.info {
    background: linear-gradient(135deg, #dbeafe, #3b82f6);
    color: #1e40af;
}

.stat-icon.primary {
    background: linear-gradient(135deg, #e0e7ff, #6366f1);
    color: #3730a3;
}

.stat-value {
    font-size: 2.5rem;
    font-weight: 700;
    line-height: 1;
    margin-bottom: 0.5rem;
}

.stat-label {
    color: #64748b;
    font-size: 0.9rem;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

/* Main Content Cards */
.content-card {
    background: white;
    border-radius: 16px;
    border: 1px solid rgba(0, 0, 0, 0.05);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    overflow: hidden;
    height: 100%;
}

.content-card-header {
    background: linear-gradient(135deg, #f8fafc, #f1f5f9);
    padding: 1.5rem 2rem;
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
}

.content-card-body {
    padding: 2rem;
}

/* Agent Status Panel */
.agent-status-controls {
    background: #f8fafc;
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 2rem;
}

.agent-item {
    background: #f8fafc;
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 1rem;
    border: 2px solid transparent;
    transition: all 0.3s ease;
}

.agent-item:hover {
    background: white;
    border-color: #e2e8f0;
    transform: translateY(-2px);
}

.agent-avatar {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    color: white;
    margin-right: 1rem;
}

.agent-avatar.online {
    background: linear-gradient(135deg, #10b981, #059669);
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.3);
}

.agent-avatar.away {
    background: linear-gradient(135deg, #f59e0b, #d97706);
    box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.3);
}

.agent-avatar.offline {
    background: linear-gradient(135deg, #6b7280, #4b5563);
}

/* Queue Items */
.queue-items {
    max-height: 600px;
    overflow-y: auto;
    padding-right: 1rem;
}

.queue-item {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 1rem;
    border-left: 4px solid #e2e8f0;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.queue-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.queue-item.priority-urgent {
    border-left-color: #dc2626;
    background: linear-gradient(135deg, #fef2f2, #ffffff);
}

.queue-item.priority-high {
    border-left-color: #f59e0b;
    background: linear-gradient(135deg, #fffbeb, #ffffff);
}

.queue-item.priority-normal {
    border-left-color: #3b82f6;
    background: linear-gradient(135deg, #eff6ff, #ffffff);
}

.queue-position {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    margin-right: 1rem;
    position: relative;
}

.priority-badge {
    position: absolute;
    top: -8px;
    right: -8px;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background: #dc2626;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.75rem;
}

.message-preview {
    background: #f8fafc;
    border-radius: 8px;
    padding: 1rem;
    margin-top: 1rem;
    border-left: 3px solid #e2e8f0;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    background: linear-gradient(135deg, #f8fafc, #ffffff);
    border-radius: 12px;
}

.empty-state-icon {
    font-size: 4rem;
    color: #cbd5e1;
    margin-bottom: 1.5rem;
}

/* Buttons */
.btn-modern {
    border-radius: 8px;
    padding: 0.75rem 1.5rem;
    font-weight: 600;
    border: none;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-modern:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.btn-success-modern {
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
}

.btn-outline-modern {
    background: white;
    border: 2px solid #e2e8f0;
    color: #64748b;
}

.btn-outline-modern:hover {
    border-color: #cbd5e1;
    background: #f8fafc;
}

/* Auto-refresh badge */
.auto-refresh-badge {
    background: linear-gradient(135deg, #ede9fe, #c4b5fd);
    color: #6d28d9;
    border-radius: 20px;
    padding: 0.5rem 1rem;
    font-size: 0.875rem;
    font-weight: 600;
}

/* Live indicator */
.live-indicator {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    color: #059669;
    font-size: 0.875rem;
    font-weight: 600;
}

.live-dot {
    width: 8px;
    height: 8px;
    background: #10b981;
    border-radius: 50%;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% {
        box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
    }
    70% {
        box-shadow: 0 0 0 10px rgba(16, 185, 129, 0);
    }
    100% {
        box-shadow: 0 0 0 0 rgba(16, 185, 129, 0);
    }
}

/* Responsive spacing */
.gap-2 { gap: 0.5rem !important; }
.gap-3 { gap: 1rem !important; }
.gap-4 { gap: 1.5rem !important; }

/* Scrollbar styling */
.queue-items::-webkit-scrollbar {
    width: 6px;
}

.queue-items::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 3px;
}

.queue-items::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 3px;
}

.queue-items::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}
</style>
@endpush

@section('content')
<div class="chat-queue-container">
    <!-- Header -->
    <div class="queue-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1>Chat Queue Management</h1>
                <p>Monitor and manage customer chat requests in real-time</p>
            </div>
            <div class="d-flex gap-3">
                <button onclick="refreshQueue()" class="btn btn-outline-modern btn-modern">
                    <i class="fas fa-sync-alt"></i> Refresh
                </button>
                <button onclick="toggleAutoRefresh()" id="autoRefreshBtn" class="btn btn-outline-modern btn-modern">
                    <i class="fas fa-play"></i> Auto Refresh
                </button>
            </div>
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="stats-container">
        <div class="row g-4">
            <div class="col-lg-3 col-md-6">
                <div class="stat-card">
                    <div class="stat-icon warning">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-value text-warning" id="waiting-count">
                        {{ $stats['waiting_customers'] }}
                    </div>
                    <div class="stat-label">Waiting Customers</div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="stat-card">
                    <div class="stat-icon success">
                        <i class="fas fa-comments"></i>
                    </div>
                    <div class="stat-value text-success" id="active-count">
                        {{ $stats['active_chats'] }}
                    </div>
                    <div class="stat-label">Active Chats</div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="stat-card">
                    <div class="stat-icon info">
                        <i class="fas fa-headset"></i>
                    </div>
                    <div class="stat-value text-info" id="available-agents">
                        {{ $stats['available_agents'] }}
                    </div>
                    <div class="stat-label">Available Agents</div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="stat-card">
                    <div class="stat-icon primary">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-value text-primary" id="avg-wait">
                        {{ round($stats['average_wait_time'] / 60, 1) }}m
                    </div>
                    <div class="stat-label">Avg Wait Time</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="row g-4">
        <!-- Agent Status Panel -->
        <div class="col-lg-4">
            <div class="content-card">
                <div class="content-card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold">Agent Status</h5>
                        <div class="live-indicator">
                            <div class="live-dot"></div>
                            Live
                        </div>
                    </div>
                </div>
                <div class="content-card-body">
                    <!-- Quick Status Controls -->
                    <div class="agent-status-controls">
                        <div class="d-grid gap-2">
                            <div class="btn-group" role="group">
                                <button onclick="updateMyStatus('online')" class="btn btn-success btn-modern">
                                    <i class="fas fa-circle"></i> Online
                                </button>
                                <button onclick="updateMyStatus('away')" class="btn btn-warning btn-modern">
                                    <i class="fas fa-pause-circle"></i> Away
                                </button>
                            </div>
                            <button onclick="fixAgentStatus()" class="btn btn-outline-modern btn-modern">
                                <i class="fas fa-tools"></i> Fix Agent Status
                            </button>
                        </div>
                    </div>

                    <!-- Agents List -->
                    <div id="agents-list">
                        @foreach($agents as $agent)
                            <div class="agent-item">
                                <div class="d-flex align-items-center">
                                    <div class="agent-avatar {{ strtolower($agent->status) }}">
                                        @if($agent->status === 'online')
                                            <i class="fas fa-headset"></i>
                                        @elseif($agent->status === 'away')
                                            <i class="fas fa-pause"></i>
                                        @else
                                            <i class="fas fa-user"></i>
                                        @endif
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="fw-bold mb-1">{{ $agent->user->name }}</div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="badge bg-{{ $agent->status === 'online' ? 'success' : ($agent->status === 'away' ? 'warning' : 'secondary') }}">
                                                {{ ucfirst($agent->status) }}
                                            </span>
                                            <small class="text-muted fw-semibold">
                                                {{ $agent->current_active_chats }}/{{ $agent->max_concurrent_chats }} chats
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Chat Queue -->
        <div class="col-lg-8">
            <div class="content-card">
                <div class="content-card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1 fw-bold">Pending Chat Queue</h5>
                            <small class="text-muted">First In, First Out (FIFO) order</small>
                        </div>
                        <div class="auto-refresh-badge">
                            <i class="fas fa-clock me-1"></i>
                            Auto-updates every 30s
                        </div>
                    </div>
                </div>
                <div class="content-card-body">
                    <div id="pending-queue">
                        @if($pendingChats->isEmpty())
                            <div class="empty-state">
                                <div class="empty-state-icon">
                                    <i class="fas fa-inbox"></i>
                                </div>
                                <h5 class="text-muted mb-2">No customers waiting in queue</h5>
                                <p class="text-muted mb-0">All caught up! New chat requests will appear here.</p>
                            </div>
                        @else
                            <div class="queue-items">
                                @foreach($pendingChats as $chat)
                                    <div class="queue-item priority-{{ $chat->priority }}" data-queue-id="{{ $chat->id }}">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <!-- Customer Info -->
                                            <div class="d-flex align-items-center flex-grow-1">
                                                <div class="queue-position">
                                                    #{{ $chat->position }}
                                                    @if($chat->priority === 'urgent')
                                                        <div class="priority-badge">
                                                            <i class="fas fa-exclamation"></i>
                                                        </div>
                                                    @endif
                                                </div>
                                                
                                                <div class="flex-grow-1">
                                                    <div class="d-flex align-items-center gap-2 mb-2">
                                                        <h6 class="mb-0 fw-bold">{{ $chat->customer->name }}</h6>
                                                        @if($chat->priority !== 'normal')
                                                            <span class="badge bg-{{ $chat->priority === 'urgent' ? 'danger' : ($chat->priority === 'high' ? 'warning' : 'info') }}">
                                                                {{ ucfirst($chat->priority) }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                    
                                                    <div class="d-flex align-items-center gap-3 text-muted small mb-2">
                                                        <span><i class="fas fa-clock me-1"></i>{{ $chat->wait_time }} min waiting</span>
                                                        @if($chat->escalation_context)
                                                            <span class="text-info"><i class="fas fa-level-up-alt me-1"></i>Escalated</span>
                                                        @endif
                                                    </div>
                                                    
                                                    @if($chat->initial_message)
                                                        <div class="message-preview">
                                                            <small class="text-muted">"{{ Str::limit($chat->initial_message, 100) }}"</small>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                            
                                            <!-- Action Buttons -->
                                            <div class="d-flex gap-2 ms-3">
                                                <button onclick="acceptChat({{ $chat->id }})" class="btn btn-success-modern btn-modern">
                                                    <i class="fas fa-headset"></i> Accept
                                                </button>
                                                <div class="dropdown">
                                                    <button class="btn btn-outline-modern btn-modern dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                        <i class="fas fa-cog"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end">
                                                        <li>
                                                            <a class="dropdown-item" href="#" onclick="assignToAgent({{ $chat->id }})">
                                                                <i class="fas fa-user-plus me-2"></i>Assign to Agent
                                                            </a>
                                                        </li>
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li>
                                                            <a class="dropdown-item text-danger" href="#" onclick="abandonChat({{ $chat->id }})">
                                                                <i class="fas fa-times me-2"></i>Remove from Queue
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Assign Agent Modal -->
<div class="modal fade" id="assignAgentModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <div>
                    <h5 class="modal-title mb-1">Assign Chat to Agent</h5>
                    <p class="text-muted small mb-0">Select an available agent to handle this chat request</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body pt-2">
                <form id="assignAgentForm">
                    <input type="hidden" id="assignQueueId" name="queue_id">
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Available Agents</label>
                        <select class="form-select form-select-lg" id="assignAgentId" name="agent_id" required>
                            <option value="">Choose an agent...</option>
                            @foreach($agents->where('status', 'online') as $agent)
                                @if($agent->canAcceptChats())
                                    <option value="{{ $agent->user_id }}">
                                        {{ $agent->user->name }} - {{ $agent->current_active_chats }}/{{ $agent->max_concurrent_chats }} active chats
                                    </option>
                                @endif
                            @endforeach
                        </select>
                        <div class="form-text">Only agents who are online and can accept new chats are shown</div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="submitAssignment()">
                    <i class="fas fa-user-plus me-1"></i> Assign Chat
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let currentQueueId = null;
let autoRefreshInterval = null;
let isAutoRefreshEnabled = false;

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    // Start auto-refresh by default
    toggleAutoRefresh();
});

function toggleAutoRefresh() {
    const btn = document.getElementById('autoRefreshBtn');
    
    if (isAutoRefreshEnabled) {
        // Stop auto-refresh
        clearInterval(autoRefreshInterval);
        isAutoRefreshEnabled = false;
        btn.innerHTML = '<i class="fas fa-play"></i> Auto Refresh';
        btn.classList.remove('btn-success');
        btn.classList.add('btn-outline-secondary');
    } else {
        // Start auto-refresh
        autoRefreshInterval = setInterval(refreshQueue, 30000);
        isAutoRefreshEnabled = true;
        btn.innerHTML = '<i class="fas fa-pause"></i> Auto Refresh';
        btn.classList.remove('btn-outline-secondary');
        btn.classList.add('btn-success');
    }
}

function refreshQueue() {
    // Show loading state
    const refreshBtn = document.querySelector('[onclick="refreshQueue()"]');
    const originalContent = refreshBtn.innerHTML;
    refreshBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Refreshing...';
    refreshBtn.disabled = true;
    
    fetch('/admin/chat-queue/data')
        .then(response => response.json())
        .then(data => {
            updateStats(data.stats);
            updatePendingQueue(data.pending_chats);
            updateAgentsList(data.agents);
            
            // Show success feedback
            showToast('Queue refreshed successfully', 'success');
        })
        .catch(error => {
            console.error('Error refreshing queue:', error);
            showToast('Failed to refresh queue', 'error');
        })
        .finally(() => {
            // Restore button state
            refreshBtn.innerHTML = originalContent;
            refreshBtn.disabled = false;
        });
}

function updateStats(stats) {
    document.getElementById('waiting-count').textContent = stats.waiting_customers;
    document.getElementById('active-count').textContent = stats.active_chats;
    document.getElementById('available-agents').textContent = stats.available_agents;
    document.getElementById('avg-wait').textContent = Math.round(stats.average_wait_time / 60 * 10) / 10 + 'm';
}

function updatePendingQueue(chats) {
    const container = document.getElementById('pending-queue');
    if (chats.length === 0) {
        container.innerHTML = `
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="fas fa-inbox"></i>
                </div>
                <h5 class="text-muted mb-2">No customers waiting in queue</h5>
                <p class="text-muted mb-0">All caught up! New chat requests will appear here.</p>
            </div>
        `;
        return;
    }
    
    // Update queue display (simplified for demo)
    // In production, you'd want to update each item individually
}

function updateAgentsList(agents) {
    // Update agents list display
    // Implementation would update the status badges and chat counts
}

function showToast(message, type = 'info') {
    // Simple toast notification
    const toast = document.createElement('div');
    toast.className = `alert alert-${type === 'success' ? 'success' : 'danger'} position-fixed top-0 end-0 m-3`;
    toast.style.zIndex = '9999';
    toast.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>
        ${message}
    `;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.remove();
    }, 3000);
}

function acceptChat(queueId) {
    if (confirm('Accept this chat?')) {
        fetch(`/admin/chat-queue/${queueId}/accept`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Chat accepted! Redirecting to conversation...');
                window.location.href = `/admin/chat#conversation-${data.conversation_id}`;
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to accept chat');
        });
    }
}

function assignToAgent(queueId) {
    currentQueueId = queueId;
    document.getElementById('assignQueueId').value = queueId;
    $('#assignAgentModal').modal('show');
}

function submitAssignment() {
    const agentId = document.getElementById('assignAgentId').value;
    if (!agentId) {
        alert('Please select an agent');
        return;
    }

    fetch(`/admin/chat-queue/${currentQueueId}/assign`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ agent_id: agentId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Chat assigned successfully!');
            $('#assignAgentModal').modal('hide');
            refreshQueue();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to assign chat');
    });
}

function abandonChat(queueId) {
    if (confirm('Remove this customer from queue?')) {
        fetch(`/admin/chat-queue/${queueId}/abandon`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Customer removed from queue');
                refreshQueue();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to remove from queue');
        });
    }
}

function updateMyStatus(status) {
    fetch('/admin/chat-queue/agent-status', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ 
            status: status,
            accepting_chats: status === 'online'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Status updated to ' + status);
            refreshQueue();
        } else {
            alert('Failed to update status');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to update status');
    });
}

function fixAgentStatus() {
    fetch('/admin/chat-queue/agent-status', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ 
            status: 'online',
            accepting_chats: true,
            current_active_chats: 0,
            force_reset: true
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('✅ Agent status fixed! You can now accept chats.');
            refreshQueue();
        } else {
            alert('❌ Failed to fix status: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('❌ Failed to fix status');
    });
}
</script>
@endsection