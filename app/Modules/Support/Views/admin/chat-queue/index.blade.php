@extends('layouts.admin')

@section('title', 'Chat Queue Management')

@section('content')
<div class="container-fluid py-4">
    <!-- Queue Statistics -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Waiting Customers</p>
                                <h5 class="font-weight-bolder mb-0" id="waiting-count">
                                    {{ $stats['waiting_customers'] }}
                                </h5>
                            </div>

                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-warning shadow text-center border-radius-md">
                                <i class="ni ni-single-02 text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Active Chats</p>
                                <h5 class="font-weight-bolder mb-0" id="active-count">
                                    {{ $stats['active_chats'] }}
                                </h5>
                            </div>

                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-success shadow text-center border-radius-md">
                                <i class="ni ni-chat-round text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Available Agents</p>
                                <h5 class="font-weight-bolder mb-0" id="available-agents">
                                    {{ $stats['available_agents'] }}
                                </h5>
                            </div>

                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                                <i class="ni ni-headphones text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Avg Wait Time</p>
                                <h5 class="font-weight-bolder mb-0" id="avg-wait">
                                    {{ round($stats['average_wait_time'] / 60, 1) }}m
                                </h5>
                            </div>

                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                                <i class="ni ni-time-alarm text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Agent Status Panel -->
        <div class="col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-header pb-0">
                    <h6>Agent Status</h6>
                    <div class="mb-3">
                        <div class="row">
                            <div class="col-6">
                                <button onclick="updateMyStatus('online')" class="btn btn-success btn-sm w-100">Online</button>
                            </div>
                            <div class="col-6">
                                <button onclick="updateMyStatus('away')" class="btn btn-warning btn-sm w-100">Away</button>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-12">
                                <button onclick="fixAgentStatus()" class="btn btn-info btn-sm w-100">
                                    <i class="fas fa-wrench"></i> Fix Agent Status
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div id="agents-list">
                        @foreach($agents as $agent)
                            <div class="d-flex align-items-center mb-3">
                                <div class="me-3">
                                    <span class="badge {{ $agent->getStatusBadgeClass() }}">
                                        {{ $agent->getStatusIcon() }} {{ ucfirst($agent->status) }}
                                    </span>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0">{{ $agent->user->name }}</h6>
                                    <small class="text-muted">
                                        {{ $agent->current_active_chats }}/{{ $agent->max_concurrent_chats }} chats
                                    </small>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Queue -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header pb-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6>Pending Chats Queue (FIFO)</h6>
                        <button onclick="refreshQueue()" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-refresh"></i> Refresh
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div id="pending-queue">
                        @if($pendingChats->isEmpty())
                            <div class="text-center py-4">
                                <p class="text-muted">No customers waiting in queue</p>
                            </div>
                        @else
                            @foreach($pendingChats as $chat)
                                <div class="border rounded p-3 mb-3" data-queue-id="{{ $chat->id }}">
                                    <div class="row align-items-center">
                                        <div class="col-md-6">
                                            <div class="d-flex align-items-center">
                                                <div class="me-3">
                                                    <span class="badge bg-info text-white">#{{ $chat->position }}</span>
                                                </div>
                                                <div>
                                                    <h6 class="mb-1">{{ $chat->customer->name }}</h6>
                                                    <small class="text-muted">
                                                        Waiting {{ $chat->wait_time }} minutes
                                                    </small>
                                                    @if($chat->priority !== 'normal')
                                                        <span class="badge badge-sm bg-{{ $chat->priority === 'urgent' ? 'danger' : 'warning' }}">
                                                            {{ ucfirst($chat->priority) }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            @if($chat->escalation_context)
                                                <small class="text-info">
                                                    <i class="fas fa-arrow-up"></i> Escalated from: {{ $chat->escalation_context['issue_category'] ?? 'Self-service' }}
                                                </small>
                                            @endif
                                            @if($chat->initial_message)
                                                <p class="small text-muted mb-0">
                                                    "{{ Str::limit($chat->initial_message, 50) }}"
                                                </p>
                                            @endif
                                        </div>
                                        <div class="col-md-3 text-end">
                                            <button onclick="acceptChat({{ $chat->id }})" class="btn btn-success btn-sm me-2">
                                                <i class="fas fa-check"></i> Accept
                                            </button>
                                            <div class="dropdown d-inline">
                                                <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-toggle="dropdown">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item" href="#" onclick="assignToAgent({{ $chat->id }})">Assign to Agent</a></li>
                                                    <li><a class="dropdown-item" href="#" onclick="abandonChat({{ $chat->id }})">Remove from Queue</a></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Assign Agent Modal -->
<div class="modal fade" id="assignAgentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Assign Chat to Agent</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="assignAgentForm">
                    <input type="hidden" id="assignQueueId" name="queue_id">
                    <div class="mb-3">
                        <label class="form-label">Select Agent</label>
                        <select class="form-select" id="assignAgentId" name="agent_id" required>
                            <option value="">Choose an agent...</option>
                            @foreach($agents->where('status', 'online') as $agent)
                                @if($agent->canAcceptChats())
                                    <option value="{{ $agent->user_id }}">
                                        {{ $agent->user->name }} ({{ $agent->current_active_chats }}/{{ $agent->max_concurrent_chats }})
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="submitAssignment()">Assign</button>
            </div>
        </div>
    </div>
</div>

<script>
let currentQueueId = null;

// Auto-refresh every 30 seconds
setInterval(refreshQueue, 30000);

function refreshQueue() {
    fetch('/admin/chat-queue/data')
        .then(response => response.json())
        .then(data => {
            updateStats(data.stats);
            updatePendingQueue(data.pending_chats);
            updateAgentsList(data.agents);
        })
        .catch(error => console.error('Error refreshing queue:', error));
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
        container.innerHTML = '<div class="text-center py-4"><p class="text-muted">No customers waiting in queue</p></div>';
        return;
    }
    
    // Update queue display (simplified for demo)
    // In production, you'd want to update each item individually
}

function updateAgentsList(agents) {
    // Update agents list display
    // Implementation would update the status badges and chat counts
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

