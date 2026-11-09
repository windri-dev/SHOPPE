<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\Auth\RoleRedirectService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminAuthController extends Controller
{
    public function __construct(
        protected RoleRedirectService $roleRedirectService
    ) {}

    public function showLoginForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            return $this->roleRedirectService->redirectBasedOnRole(Auth::user());
        }

        return view('admin.login');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();

            // Validasi role pengguna setelah proses autentikasi berhasil:
            // Admin diarahkan ke Dashboard Admin, User diarahkan ke Halaman User
            return $this->roleRedirectService->redirectBasedOnRole($user);
        }

        return back()->withErrors(['email' => 'Kredensial admin tidak cocok dengan data kami.'])->onlyInput('email');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('success', 'Sesi admin telah berakhir.');
    }
}
