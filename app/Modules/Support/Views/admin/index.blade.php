@extends('layouts.admin')

@section('title', 'Support Tickets Management')

@section('content')
<div class="container-fluid py-4">
    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Total Tickets</p>
                                <h5 class="font-weight-bolder mb-0">{{ $stats['total'] }}</h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                                <i class="ni ni-single-copy-04 text-lg opacity-10" aria-hidden="true"></i>
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
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Open Tickets</p>
                                <h5 class="font-weight-bolder mb-0">{{ $stats['open'] }}</h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-warning shadow text-center border-radius-md">
                                <i class="ni ni-active-40 text-lg opacity-10" aria-hidden="true"></i>
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
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Unassigned</p>
                                <h5 class="font-weight-bolder mb-0">{{ $stats['unassigned'] }}</h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                                <i class="ni ni-user-run text-lg opacity-10" aria-hidden="true"></i>
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
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">My Tickets</p>
                                <h5 class="font-weight-bolder mb-0">{{ $stats['my_tickets'] }}</h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-success shadow text-center border-radius-md">
                                <i class="ni ni-badge text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-0">
                    <h6>Tickets Filter</h6>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.tickets.index') }}">
                        <div class="row">
                            <div class="col-md-2">
                                <select name="status" class="form-select">
                                    <option value="">All Status</option>
                                    <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>Open</option>
                                    <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                    <option value="waiting_customer" {{ request('status') === 'waiting_customer' ? 'selected' : '' }}>Waiting Customer</option>
                                    <option value="waiting_agent" {{ request('status') === 'waiting_agent' ? 'selected' : '' }}>Waiting Agent</option>
                                    <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Resolved</option>
                                    <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Closed</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="priority" class="form-select">
                                    <option value="">All Priority</option>
                                    <option value="urgent" {{ request('priority') === 'urgent' ? 'selected' : '' }}>Urgent</option>
                                    <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>High</option>
                                    <option value="normal" {{ request('priority') === 'normal' ? 'selected' : '' }}>Normal</option>
                                    <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Low</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="assigned_to" class="form-select">
                                    <option value="">All Assignments</option>
                                    <option value="unassigned" {{ request('assigned_to') === 'unassigned' ? 'selected' : '' }}>Unassigned</option>
                                    <option value="me" {{ request('assigned_to') === 'me' ? 'selected' : '' }}>My Tickets</option>
                                    @foreach($agents as $agent)
                                        <option value="{{ $agent->id }}" {{ request('assigned_to') == $agent->id ? 'selected' : '' }}>
                                            {{ $agent->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <input type="text" name="search" class="form-control" placeholder="Search tickets..." value="{{ request('search') }}">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary">Filter</button>
                                <a href="{{ route('admin.tickets.index') }}" class="btn btn-outline-secondary">Clear</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Tickets Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-0">
                    <div class="d-flex justify-content-between">
                        <h6>Support Tickets</h6>
                        <button class="btn btn-outline-primary btn-sm" onclick="refreshTickets()">
                            <i class="fas fa-refresh"></i> Refresh
                        </button>
                    </div>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    @if($tickets->count() > 0)
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Ticket</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Customer</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Subject</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Priority</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Assigned To</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Created</th>
                                        <th class="text-secondary opacity-7">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($tickets as $ticket)
                                        <tr>
                                            <td>
                                                <div class="d-flex px-2 py-1">
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm">{{ $ticket->ticket_number }}</h6>
                                                        <p class="text-xs text-secondary mb-0">{{ $ticket->category_display }}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex px-2 py-1">
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm">{{ $ticket->user->name }}</h6>
                                                        <p class="text-xs text-secondary mb-0">{{ $ticket->user->email }}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <p class="text-xs font-weight-bold mb-0">{{ Str::limit($ticket->subject, 40) }}</p>
                                                <p class="text-xs text-secondary mb-0">{{ Str::limit($ticket->description, 60) }}</p>
                                            </td>
                                            <td class="align-middle text-center text-sm">
                                                <span class="badge badge-sm {{ $ticket->status_badge_class }}">
                                                    {{ ucwords(str_replace('_', ' ', $ticket->status)) }}
                                                </span>
                                            </td>
                                            <td class="align-middle text-center text-sm">
                                                <span class="badge badge-sm {{ $ticket->priority_badge_class }}">
                                                    {{ ucfirst($ticket->priority) }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($ticket->assignedAgent)
                                                    <div class="d-flex px-2 py-1">
                                                        <div class="d-flex flex-column justify-content-center">
                                                            <h6 class="mb-0 text-sm">{{ $ticket->assignedAgent->name }}</h6>
                                                        </div>
                                                    </div>
                                                @else
                                                    <span class="text-secondary text-xs">Unassigned</span>
                                                @endif
                                            </td>
                                            <td class="align-middle text-center">
                                                <span class="text-secondary text-xs font-weight-bold">{{ $ticket->created_at->format('M j, Y') }}</span>
                                            </td>
                                            <td class="align-middle">
                                                <a href="{{ route('admin.tickets.show', $ticket) }}" class="btn btn-link text-dark px-3 mb-0">
                                                    <i class="fas fa-eye text-dark me-2"></i>View
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        @if($tickets->hasPages())
                            <div class="d-flex justify-content-center mt-3">
                                {{ $tickets->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-ticket-alt fa-3x text-secondary mb-3"></i>
                            <h6 class="text-secondary">No tickets found</h6>
                            <p class="text-xs text-secondary">No support tickets match your current filters.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function refreshTickets() {
    window.location.reload();
}
</script>
@endsection