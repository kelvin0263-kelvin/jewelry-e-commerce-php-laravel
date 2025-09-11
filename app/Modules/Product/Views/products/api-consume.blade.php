@extends('layouts.app')

@section('title', 'API Integration Demo')

@push('styles')
<style>
    .api-demo-container { max-width: 1100px; margin: 0 auto; padding: 2rem; }
    .api-card { background: #fff; border-radius: 12px; box-shadow: 0 4px 16px rgba(0,0,0,0.08); padding: 1.5rem; }
    .api-header { display:flex; justify-content: space-between; align-items:center; margin-bottom: 1rem; }
    .badge { display:inline-block; padding: 0.25rem 0.6rem; border-radius: 999px; font-size: 0.8rem; }
    .badge-http { background: #e8f4ff; color: #0b65c2; border: 1px solid #b6dbff; }
    .badge-internal { background: #f0f9eb; color: #2b7a0b; border: 1px solid #c7efb9; }
    .error { background: #fff5f5; color: #c53030; border: 1px solid #fed7d7; padding: 0.75rem 1rem; border-radius: 8px; margin: 1rem 0; }
    table { width: 100%; border-collapse: collapse; }
    th, td { padding: 0.75rem; border-bottom: 1px solid #eee; font-size: 0.95rem; }
    th { text-align: left; color: #555; font-weight: 600; background: #fafafa; }
    .controls { display:flex; gap: 10px; margin-bottom: 1rem; }
    .btn { padding: 0.5rem 0.9rem; border-radius: 8px; border: 1px solid #ddd; text-decoration: none; color: #222; font-weight: 600; }
    .btn-primary { background: #d4af37; color: #fff; border: none; }
    .btn-primary:hover { background: #b8941f; color: #fff; }
    .muted { color: #777; font-size: 0.9rem; }
    .nowrap { white-space: nowrap; }
    .empty { text-align: center; padding: 2rem; color: #777; }
    .small { font-size: 0.85rem; color: #666; }
    .right { text-align: right; }
</style>
@endpush

@section('content')
<div class="api-demo-container">
    <div class="api-card">
        <div class="api-header">
            <div>
                <h2 style="margin:0 0 6px; font-weight:600;">External API Demo — Inventory</h2>
                <div class="muted">This Product page consumes the Inventory module's API.</div>
            </div>
            <span class="badge {{ $used_http ? 'badge-http' : 'badge-internal' }}">{{ $used_http ? 'HTTP API' : 'Internal Service' }}</span>
        </div>

        <div class="controls">
            <a class="btn" href="{{ url()->current() }}?use_api=0">Use Internal (no HTTP)</a>
            <a class="btn btn-primary" href="{{ url()->current() }}?use_api=1">Use HTTP API</a>
            <a class="btn" href="{{ route('products.index') }}">Back to Products</a>
        </div>

        @if($error)
            <div class="error">{{ $error }}</div>
        @endif

        @if(empty($items))
            <div class="empty">No inventory items found.</div>
        @else
            <table>
                <thead>
                    <tr>
                        <th class="nowrap">ID</th>
                        <th>Name</th>
                        <th>Type</th>
                        <th class="right">Price</th>
                        <th>Status</th>
                        <th class="right">Variations</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $item)
                        <tr>
                            <td class="nowrap">{{ $item['id'] ?? '-' }}</td>
                            <td>{{ $item['name'] ?? '-' }}</td>
                            <td>{{ $item['type'] ?? '-' }}</td>
                            <td class="right">{{ isset($item['price']) ? number_format((float)$item['price'], 2) : '-' }}</td>
                            <td>{{ $item['status'] ?? '-' }}</td>
                            <td class="right">{{ isset($item['variations']) ? count($item['variations']) : 0 }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="small" style="margin-top: 10px;">Source: {{ $used_http ? url('/api/inventory') : 'internal Eloquent query' }}</div>
        @endif
    </div>
</div>
@endsection

