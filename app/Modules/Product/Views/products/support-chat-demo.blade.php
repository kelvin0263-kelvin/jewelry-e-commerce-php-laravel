@extends('layouts.app')

@section('title', 'Support Chat API Demo')

@push('styles')
<style>
    .container { max-width: 1200px; margin: 0 auto; padding: 2rem; }
    .card { background: #fff; border-radius: 12px; box-shadow: 0 4px 16px rgba(0,0,0,0.08); padding: 1.25rem; }
    .header { display:flex; align-items:center; justify-content: space-between; margin-bottom: 1rem; }
    .badge { display:inline-block; padding: 0.25rem 0.6rem; border-radius: 999px; font-size: 0.8rem; }
    .badge-http { background: #e8f4ff; color: #0b65c2; border: 1px solid #b6dbff; }
    .badge-internal { background: #f0f9eb; color: #2b7a0b; border: 1px solid #c7efb9; }
    .controls { display:flex; gap: 10px; flex-wrap: wrap; margin-bottom: 1rem; }
    .btn { padding: 0.5rem 0.9rem; border-radius: 8px; border: 1px solid #ddd; text-decoration: none; color: #222; font-weight: 600; }
    .btn-primary { background: #d4af37; color: #fff; border: none; }
    .btn-primary:hover { background: #b8941f; color: #fff; }
    .btn:disabled { opacity: 0.6; cursor: not-allowed; }
    .grid { display:grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
    .error { background: #fff5f5; color: #c53030; border: 1px solid #fed7d7; padding: 0.75rem 1rem; border-radius: 8px; margin: 1rem 0; }
    table { width: 100%; border-collapse: collapse; }
    th, td { padding: 0.6rem; border-bottom: 1px solid #eee; font-size: 0.95rem; }
    th { text-align:left; background:#fafafa; color:#555; }
    .muted { color:#777; font-size: 0.9rem; }
    .small { font-size: 0.85rem; color: #666; }
    .nowrap { white-space: nowrap; }
    .message { padding: 0.4rem 0.6rem; border-bottom: 1px dashed #eee; }
    .message .meta { font-size: 0.8rem; color:#666; }
    .message .body { margin-top: 4px; }
    .right { text-align: right; }
    .link { color:#0b65c2; text-decoration: none; }
    .link:hover { text-decoration: underline; }
</style>
@endpush

@section('content')
<div class="container">
    <div class="card">
        <div class="header">
            <div>
                <h2 style="margin:0 0 6px; font-weight:600;">Support Chat API Demo</h2>
                <div class="muted">Consumes Support module chat endpoints via HTTP (Sanctum) or internal.</div>
            </div>
            <span class="badge {{ $used_http ? 'badge-http' : 'badge-internal' }}">{{ $used_http ? 'HTTP API' : 'Internal Service' }}</span>
        </div>

        <div class="controls">
            <a class="btn" href="{{ url()->current() }}?use_api=0">Use Internal</a>
            <a class="btn btn-primary" href="{{ url()->current() }}?use_api=1">Use HTTP API</a>
            <a class="btn" href="{{ route('products.api-demo') }}">Inventory API Demo</a>
            @if($used_http)
                <a class="btn" href="{{ url()->current() }}?use_api=1&action=start">Start Chat (HTTP)</a>
            @endif
        </div>

        @if($used_http)
            <div class="small" style="margin-bottom: 0.75rem;">
                Tip: Must be authenticated. Either login and revisit, or append <code>?token=YOUR_SANCTUM_TOKEN</code>.
            </div>
        @endif

        @if($error)
            <div class="error">{{ $error }}</div>
        @endif

        <div class="grid">
            <div>
                <h3 style="margin: 0 0 8px;">Conversations</h3>
                @if(empty($conversations))
                    <div class="small">No conversations found.</div>
                @else
                    <table>
                        <thead>
                            <tr>
                                <th class="nowrap">ID</th>
                                <th>User</th>
                                <th>Agent</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($conversations as $c)
                            <tr>
                                <td class="nowrap">
                                    <a class="link" href="{{ url()->current() }}?use_api={{ $used_http ? 1 : 0 }}&conversation_id={{ $c['id'] ?? '' }}">{{ $c['id'] ?? '-' }}</a>
                                </td>
                                <td>{{ data_get($c, 'user.name', '-') }}</td>
                                <td>{{ data_get($c, 'agent.name', '-') }}</td>
                                <td>{{ $c['status'] ?? '-' }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
            <div>
                <h3 style="margin: 0 0 8px;">Messages @if($conversation_id) <span class="small">(Conversation #{{ $conversation_id }})</span>@endif</h3>
                @if(empty($messages))
                    <div class="small">Select a conversation to view its messages.</div>
                @else
                    <div style="max-height: 400px; overflow:auto; border:1px solid #eee; border-radius: 8px; padding: 8px;">
                        @foreach($messages as $m)
                            <div class="message">
                                <div class="meta">{{ data_get($m, 'created_at', '-') }} — {{ data_get($m, 'user.name') ?? 'System' }}</div>
                                <div class="body">{{ $m['body'] ?? '' }}</div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

