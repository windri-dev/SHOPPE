<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Enums\UserRole;
use App\Services\Auth\RoleRedirectService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class AccountController extends Controller
{
    public function __construct(
        protected RoleRedirectService $roleRedirectService
    ) {}

    public function index(): View|RedirectResponse
    {
        // Jika pengguna aktif memiliki role admin, arahkan ke Dashboard Admin
        if (Auth::check() && Auth::user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        $user = Auth::user();
        $orders = $user ? $user->orders()->with('items')->latest()->get() : collect();

        return view('pages.account', compact('user', 'orders'));
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();

            // Mekanisme Role-Based Login Redirect:
            // - Admin: Diarahkan langsung ke halaman Dashboard Admin
            // - User: Diarahkan langsung ke Halaman User
            return $this->roleRedirectService->redirectBasedOnRole($user);
        }

        return back()->withErrors([
            'email' => 'Email atau kata sandi yang Anda masukkan salah.',
        ])->onlyInput('email');
    }

    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => UserRole::CUSTOMER,
        ]);

        Auth::login($user);

        return $this->roleRedirectService->redirectBasedOnRole($user);
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Anda telah keluar dari akun.');
    }
}
