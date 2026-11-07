<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SHOPPE - Restricted Admin Login</title>
  <meta name="robots" content="noindex, nofollow">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&display=swap" rel="stylesheet">
  @vite(['resources/css/shoppe.css'])
</head>
<body style="background: #111; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; font-family: 'DM Sans', sans-serif;">
  <div class="admin-auth-card" style="background: #1C1C1C; border: 1px solid #333; padding: 40px; border-radius: 8px; width: 100%; max-width: 440px; color: #fff;">
    <div style="display: inline-block; background: rgba(161, 138, 104, 0.2); color: #A18A68; padding: 4px 10px; border-radius: 4px; font-size: 11px; font-weight: 600; letter-spacing: 1px; text-transform: uppercase; margin-bottom: 15px;">
      Restricted &bull; Store Staff Only
    </div>
    <h1 style="font-size: 24px; font-weight: 600; letter-spacing: 2px; margin: 0 0 5px;">SHOPPE</h1>
    <h2 style="font-size: 16px; color: #A18A68; margin: 0 0 20px; font-weight: 400;">Master Store Management</h2>

    @if($errors->any())
      <div style="background: rgba(220, 53, 69, 0.2); border: 1px solid #dc3545; color: #ff6b6b; padding: 12px; border-radius: 4px; margin-bottom: 20px; font-size: 13px;">
        {{ $errors->first() }}
      </div>
    @endif

    <form action="{{ route('admin.login.submit') }}" method="POST">
      @csrf
      <div style="margin-bottom: 18px;">
        <label style="display: block; font-size: 13px; color: #ccc; margin-bottom: 6px;">Email Pengelola</label>
        <input type="email" name="email" value="{{ old('email', 'admin@shoppe.com') }}" required style="width: 100%; padding: 12px; background: #2A2A2A; border: 1px solid #444; border-radius: 4px; color: #fff; font-size: 14px;">
      </div>
      <div style="margin-bottom: 24px;">
        <label style="display: block; font-size: 13px; color: #ccc; margin-bottom: 6px;">Password</label>
        <input type="password" name="password" value="admin123" required style="width: 100%; padding: 12px; background: #2A2A2A; border: 1px solid #444; border-radius: 4px; color: #fff; font-size: 14px;">
      </div>
      <button type="submit" style="width: 100%; padding: 14px; background: #A18A68; color: #fff; border: none; border-radius: 4px; font-weight: 600; font-size: 14px; cursor: pointer;">
        Masuk ke Dashboard Toko &rarr;
      </button>
    </form>

    <div style="margin-top: 25px; padding-top: 20px; border-top: 1px solid #333; text-align: center; font-size: 13px;">
      <a href="{{ route('home') }}" style="color: #888; text-decoration: none;">&larr; Kembali ke Etalase Toko Publik</a>
    </div>
  </div>
</body>
</html>
