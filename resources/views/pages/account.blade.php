@extends('layouts.storefront')

@section('title', 'My Account - SHOPPE')

@section('content')
<div class="container account-page">
  <div class="account-header">
    <h1 class="account-title">My Account</h1>
  </div>

  {{-- Flash Notifications --}}
  <div style="max-width: 500px; margin: 0 auto 30px;">
    @if(session('success'))
      <div style="background: #e8f5e9; border: 1px solid #c8e6c9; color: #2e7d32; padding: 14px 18px; border-radius: 6px; margin-bottom: 20px; font-size: 14px; text-align: center;">
        {{ session('success') }}
      </div>
    @endif

    @if(session('info'))
      <div style="background: #e3f2fd; border: 1px solid #bbdefb; color: #1565c0; padding: 14px 18px; border-radius: 6px; margin-bottom: 20px; font-size: 14px; text-align: center;">
        {{ session('info') }}
      </div>
    @endif

    @if(session('error'))
      <div style="background: #ffebee; border: 1px solid #ffcdd2; color: #c62828; padding: 14px 18px; border-radius: 6px; margin-bottom: 20px; font-size: 14px; text-align: center;">
        {{ session('error') }}
      </div>
    @endif

    @if($errors->any())
      <div style="background: #ffebee; border: 1px solid #ffcdd2; color: #c62828; padding: 14px 18px; border-radius: 6px; margin-bottom: 20px; font-size: 14px; text-align: center;">
        {{ $errors->first() }}
      </div>
    @endif
  </div>

  @auth
    <!-- Customer Profile & Orders Dashboard -->
    <div class="user-dashboard-wrap">
      <div class="account-welcome" style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #eee; padding-bottom: 24px; margin-bottom: 36px;">
        <div>
          <h2 style="font-size: 24px; font-weight: 500; margin: 0; color: var(--color-black);">Halo, {{ auth()->user()->name }}</h2>
          <p style="color: #707070; margin: 6px 0 0; font-size: 14px;">{{ auth()->user()->email }} &bull; Member Customer</p>
        </div>
        <form action="{{ route('account.logout') }}" method="POST">
          @csrf
          <button type="submit" class="btn btn-outline" style="font-size: 13px; padding: 10px 22px;">Keluar (Logout)</button>
        </form>
      </div>

      <div class="dashboard-table-wrap">
        <h3 style="font-size: 20px; font-weight: 500; margin-bottom: 20px; color: var(--color-black);">Riwayat Pesanan Anda</h3>
        @if($orders->count() > 0)
          <table class="dashboard-table">
            <thead>
              <tr>
                <th>No. Order</th>
                <th>Tanggal</th>
                <th>Status</th>
                <th style="text-align: right;">Total</th>
                <th style="text-align: right;">Aksi</th>
              </tr>
            </thead>
            <tbody>
              @foreach($orders as $ord)
                <tr>
                  <td><strong>{{ $ord->order_number }}</strong></td>
                  <td>{{ $ord->created_at->format('d M Y') }}</td>
                  <td>
                    <span class="badge {{ $ord->status->badgeClass() }}">
                      {{ $ord->status->label() }}
                    </span>
                  </td>
                  <td style="text-align: right; font-weight: 500;">{{ $ord->formatted_grand_total }}</td>
                  <td style="text-align: right;">
                    <a href="{{ route('order.show', $ord->order_number) }}" class="table-gold-link">Lihat Resi</a>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        @else
          <div style="text-align: center; padding: 48px; background: #FAF9F6; border: 1px dashed #D8D8D8; border-radius: 6px;">
            <p style="color: #707070; font-size: 15px; margin-bottom: 16px;">Anda belum memiliki riwayat pesanan.</p>
            <a href="{{ route('shop.index') }}" class="btn btn-primary" style="font-size: 13px;">Mulai Belanja</a>
          </div>
        @endif
      </div>
    </div>
  @else
    <!-- Guest Auth: Segmented Tabs & Clean Underline Forms -->
    <div class="account-wrapper">
      <div class="account-tabs" role="tablist">
        <button type="button" class="account-tab-btn {{ $errors->has('name') || $errors->has('password_confirmation') ? '' : 'active' }}" data-tab="loginPanel" role="tab" aria-selected="{{ $errors->has('name') || $errors->has('password_confirmation') ? 'false' : 'true' }}">
          Sign In
        </button>
        <button type="button" class="account-tab-btn {{ $errors->has('name') || $errors->has('password_confirmation') ? 'active' : '' }}" data-tab="registerPanel" role="tab" aria-selected="{{ $errors->has('name') || $errors->has('password_confirmation') ? 'true' : 'false' }}">
          Register
        </button>
      </div>

      <!-- Sign In Panel -->
      <div class="account-form-panel {{ $errors->has('name') || $errors->has('password_confirmation') ? '' : 'active' }}" id="loginPanel" role="tabpanel">
        <form action="{{ route('account.login') }}" method="POST" class="account-form">
          @csrf
          <div class="account-input-wrap">
            <input type="email" name="email" value="{{ old('email') }}" placeholder="Email *" class="account-input" required autocomplete="email">
          </div>
          <div class="account-input-wrap">
            <input type="password" name="password" placeholder="Password *" class="account-input" required autocomplete="current-password">
          </div>
          <div class="account-checkbox-wrap">
            <label class="account-checkbox-label">
              <input type="checkbox" name="remember" class="account-checkbox-input">
              <span>Remember me</span>
            </label>
          </div>
          <button type="submit" class="account-submit-btn">SIGN IN</button>
        </form>
      </div>

      <!-- Register Panel -->
      <div class="account-form-panel {{ $errors->has('name') || $errors->has('password_confirmation') ? 'active' : '' }}" id="registerPanel" role="tabpanel">
        <form action="{{ route('account.register') }}" method="POST" class="account-form">
          @csrf
          <div class="account-input-wrap">
            <input type="text" name="name" value="{{ old('name') }}" placeholder="Full Name *" class="account-input" required autocomplete="name">
          </div>
          <div class="account-input-wrap">
            <input type="email" name="email" value="{{ old('email') }}" placeholder="Email *" class="account-input" required autocomplete="email">
          </div>
          <div class="account-input-wrap">
            <input type="password" name="password" placeholder="Password *" class="account-input" required autocomplete="new-password">
          </div>
          <div class="account-input-wrap">
            <input type="password" name="password_confirmation" placeholder="Confirm Password *" class="account-input" required autocomplete="new-password">
          </div>
          <button type="submit" class="account-submit-btn">REGISTER</button>
        </form>
      </div>
    </div>
  @endauth
</div>

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const tabBtns = document.querySelectorAll('.account-tab-btn');
    const panels = document.querySelectorAll('.account-form-panel');

    tabBtns.forEach(function (btn) {
      btn.addEventListener('click', function () {
        const targetId = this.getAttribute('data-tab');

        tabBtns.forEach(function (b) {
          b.classList.remove('active');
          b.setAttribute('aria-selected', 'false');
        });
        panels.forEach(function (p) {
          p.classList.remove('active');
        });

        this.classList.add('active');
        this.setAttribute('aria-selected', 'true');

        const targetPanel = document.getElementById(targetId);
        if (targetPanel) {
          targetPanel.classList.add('active');
        }
      });
    });
  });
</script>
@endpush
@endsection
