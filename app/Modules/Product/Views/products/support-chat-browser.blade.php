@extends('layouts.app')

@section('title', 'Support Chat — Browser/XHR Demo')

@push('styles')
<style>
  .container { max-width: 1000px; margin: 0 auto; padding: 2rem; }
  .card { background: #fff; border-radius: 12px; box-shadow: 0 4px 16px rgba(0,0,0,0.08); padding: 1.25rem; margin-bottom: 1rem; }
  .muted { color:#777; font-size: .95rem; }
  .btn { padding: .55rem .9rem; border-radius: 8px; border: 1px solid #ddd; text-decoration: none; color:#222; font-weight:600; }
  .btn-primary { background: #d4af37; color: #fff; border:none; }
  .btn-primary:hover { background:#b8941f; color:#fff; }
  pre { background:#f9fafb; border:1px solid #eee; padding:10px; border-radius:8px; max-height:320px; overflow:auto; }
  code { background:#f1f5f9; padding:2px 4px; border-radius: 4px; }
</style>
@endpush

@section('content')
<div class="container">
  <div class="card">
    <h2 style="margin:0 0 6px; font-weight:600;">Browser/XHR Chat Demo</h2>
    <div class="muted">This page triggers Support APIs from the browser (not PHP), so no self-call deadlock can occur.</div>
  </div>

  <div class="card">
    <h3 style="margin-top:0;">Chat Widget Call</h3>
    <p class="muted">Uses <code>window.SupportChat.open()</code> defined by the widget. If not logged in, you’ll be redirected to login. When logged in, it will call <code>/api/support/chat/start</code> via XHR.</p>
    <button id="ask-support" class="btn btn-primary">Chat with us</button>
    <pre style="margin-top:10px;"><code>document.getElementById('ask-support').addEventListener('click', async () => {
  const ctx = { source: 'product_page', product_id: '{{ data_get($ctx, 'product_id') }}', product_name: '{{ data_get($ctx, 'product_name') }}' };
  try {
    await window.SupportChat.open({
      autoStart: true,
      initial_message: 'Hi, I need help with this product.',
      escalation_context: ctx
    });
  } catch (e) {
    console.error(e);
    alert('Unable to start chat right now.');
  }
});</code></pre>
  </div>

  <div class="card">
    <h3 style="margin-top:0;">XHR Test: Conversations (GET)</h3>
    <p class="muted">Browser fetch to <code>/api/support/chat/conversations</code>. You may see <code>401</code> if not logged in — that’s expected — but you will not see a cURL 28 because this is not a server self-call.</p>
    <div style="display:flex; gap:10px; margin-bottom:8px;">
      <button id="test-xhr" class="btn">Run XHR</button>
      <a href="{{ url('/login') }}" class="btn">Login</a>
    </div>
    <pre id="xhr-output">Click "Run XHR" to test…</pre>
  </div>
</div>

<script>
  // Chat widget button
  document.getElementById('ask-support')?.addEventListener('click', async () => {
    const ctx = @json($ctx);
    try {
      await window.SupportChat.open({
        autoStart: true,
        initial_message: 'Hi, I need help with this product.',
        escalation_context: ctx
      });
    } catch (e) {
      console.error(e);
      alert('Unable to start chat right now.');
    }
  });

  async function sanctumBoot() {
    // Ensure XSRF cookie for stateful Sanctum
    if (!document.cookie.includes('XSRF-TOKEN')) {
      await fetch('/sanctum/csrf-cookie', { credentials: 'include' });
    }
  }

  document.getElementById('test-xhr')?.addEventListener('click', async () => {
    const out = document.getElementById('xhr-output');
    out.textContent = 'Running…';
    try {
      await sanctumBoot();
      const resp = await fetch('/api/support/chat/conversations', {
        credentials: 'include',
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
      });
      const text = await resp.text();
      out.textContent = `Status: ${resp.status}\n` + text.slice(0, 1200);
    } catch (e) {
      out.textContent = 'Error: ' + (e && e.message ? e.message : e);
    }
  });
</script>
@endsection

