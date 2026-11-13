<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>PUTRIJALVA - Restricted Admin Login</title>
  <meta name="robots" content="noindex, nofollow">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&display=swap" rel="stylesheet">
  @vite(['resources/css/shoppe.css'])
</head>
<body>
  <div class="admin-auth-gate">
    <div class="admin-auth-card">
      <div style="text-align: center; margin-bottom: 12px;">
        <img src="{{ asset('images/logo-stacked.png') }}" alt="PUTRIJALVA" style="height: 78px; width: auto; object-fit: contain; margin: 0 auto 12px; display: block;">
        <span class="admin-auth-badge">Restricted &bull; Store Staff Only</span>
      </div>
      <h2 class="admin-auth-title">Master Store Management</h2>
      <p class="admin-auth-subtitle">Silakan masuk menggunakan kredensial pengelola toko internal Anda.</p>

      @if(session('error'))
        <div class="admin-auth-error" style="margin-bottom: 16px;">
          {{ session('error') }}
        </div>
      @endif

      @if($errors->any())
        <div class="admin-auth-error" style="margin-bottom: 16px;">
          {{ $errors->first() }}
        </div>
      @endif

      <form action="{{ route('admin.login.submit') }}" method="POST" class="admin-auth-form">
        @csrf
        <div class="admin-form-group">
          <label for="adminEmail">Email Pengelola</label>
          <input
            type="email"
            id="adminEmail"
            name="email"
            value="{{ old('email', 'admin@shoppe.com') }}"
            class="admin-form-input"
            required
            autocomplete="email"
          >
        </div>

        <div class="admin-form-group">
          <label for="adminPassword">Password</label>
          <input
            type="password"
            id="adminPassword"
            name="password"
            value="admin123"
            class="admin-form-input"
            required
            autocomplete="current-password"
          >
        </div>

        <button type="submit" class="btn-admin-auth">
          Masuk ke Dashboard Toko &rarr;
        </button>
      </form>

      <div class="admin-auth-footer">
        <a href="{{ route('home') }}" class="admin-auth-back-link">&larr; Kembali ke Etalase Toko Publik</a>
      </div>
    </div>
  </div>
</body>
</html>
