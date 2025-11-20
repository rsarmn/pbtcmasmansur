@extends('layout')
@section('content')
<style>
  .login-wrap{display:grid;grid-template-columns:1.1fr 1fr;gap:22px;align-items:stretch}
  .panel-left{background:var(--brand);color:#fff;border-radius:18px;padding:28px 24px;display:grid;place-items:center}
  .panel-right{background:#fff;border-radius:18px;padding:28px 24px;box-shadow:0 8px 24px rgba(0,0,0,.06)}
  .input-soft{background:#f3f3f5;border:0;border-radius:8px;padding:10px 12px;width:100%}
  @media(max-width:900px){.login-wrap{grid-template-columns:1fr}}
  .login-btn{background:var(--brand);border:0;color:#fff;padding:10px 14px;border-radius:10px;font-weight:700;width:100%}
</style>

<div class="container mt-4">
  <div class="login-wrap">
    <div class="panel-left">
      <div class="text-center">
        <div style="font-size:22px;font-weight:800;margin-bottom:12px">Login Admin</div>
        <img src="{{ asset('img/admin-page.png') }}" alt="admin" style="max-width:280px;width:100%">
      </div>
    </div>
    <div class="panel-right">
      <h5 class="mb-3">USER LOGIN</h5>
      <form method="POST" action="{{ route('auth.login.post') }}">
        @csrf
        <div class="mb-3">
          <input class="input-soft" type="email" name="email" placeholder="Email" required>
        </div>
        <div class="mb-3">
          <input class="input-soft" type="password" name="password" placeholder="Password" required>
        </div>
        <button class="login-btn">LOGIN</button>
      </form>
    </div>
  </div>
</div>
@endsection
