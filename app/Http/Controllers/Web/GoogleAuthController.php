<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class GoogleAuthController extends Controller
{
    /**
     * Redirect pengguna ke halaman persetujuan Google OAuth 2.0.
     */
    public function redirect(): RedirectResponse
    {
        $clientId = config('services.google.client_id');
        $redirectUri = config('services.google.redirect') ?: route('auth.google.callback');

        if (empty($clientId)) {
            return redirect()->route('account.index')->with(
                'error',
                __('Google OAuth belum dikonfigurasi. Silakan lengkapi GOOGLE_CLIENT_ID dan GOOGLE_CLIENT_SECRET pada berkas .env Anda.')
            );
        }

        $state = Str::random(40);
        session(['google_oauth_state' => $state]);

        $query = http_build_query([
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'response_type' => 'code',
            'scope' => 'openid profile email',
            'state' => $state,
            'access_type' => 'online',
            'prompt' => 'select_account',
        ]);

        return redirect()->away('https://accounts.google.com/o/oauth2/v2/auth?'.$query);
    }

    /**
     * Tangani callback dari Google OAuth setelah pengguna memberikan persetujuan.
     */
    public function callback(Request $request): RedirectResponse
    {
        // Tangani jika pengguna membatalkan izin
        if ($request->has('error')) {
            Log::info('Google OAuth dibatalkan oleh pengguna: '.$request->input('error'));

            return redirect()->route('account.index')->with('info', __('Proses masuk dengan akun Google dibatalkan.'));
        }

        $code = (string) $request->input('code');
        $state = (string) $request->input('state');
        $savedState = (string) session('google_oauth_state');

        // Verifikasi token state anti-CSRF
        if (empty($code) || empty($state) || ! hash_equals($savedState, $state)) {
            return redirect()->route('account.index')->with('error', __('Sesi autentikasi Google tidak valid atau telah kedaluwarsa. Silakan coba kembali.'));
        }

        session()->forget('google_oauth_state');

        $clientId = config('services.google.client_id');
        $clientSecret = config('services.google.client_secret');
        $redirectUri = config('services.google.redirect') ?: route('auth.google.callback');

        try {
            // 1. Tukarkan kode otorisasi dengan Access Token dari Google
            $tokenResponse = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                'code' => $code,
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'redirect_uri' => $redirectUri,
                'grant_type' => 'authorization_code',
            ]);

            if ($tokenResponse->failed()) {
                Log::error('Google OAuth Token Exchange Failed', ['body' => $tokenResponse->body()]);

                return redirect()->route('account.index')->with('error', __('Gagal menukar token otentikasi Google. Silakan coba kembali.'));
            }

            $accessToken = $tokenResponse->json('access_token');

            // 2. Ambil profil pengguna dari Google UserInfo Endpoint
            $userResponse = Http::withToken($accessToken)->get('https://www.googleapis.com/oauth2/v3/userinfo');

            if ($userResponse->failed()) {
                Log::error('Google OAuth UserInfo Failed', ['body' => $userResponse->body()]);

                return redirect()->route('account.index')->with('error', __('Gagal mengambil data profil Google Anda.'));
            }

            $googleUser = $userResponse->json();
            $googleId = (string) ($googleUser['sub'] ?? '');
            $email = strtolower(trim((string) ($googleUser['email'] ?? '')));
            $name = (string) ($googleUser['name'] ?? 'Pelanggan PUTRIJALVA');
            $avatar = (string) ($googleUser['picture'] ?? '');

            if (empty($email)) {
                return redirect()->route('account.index')->with('error', __('Alamat email tidak ditemukan dari akun Google Anda.'));
            }

            // 3. Temukan atau buat akun pelanggan di database
            $user = User::where('google_id', $googleId)
                ->orWhere('email', $email)
                ->first();

            if ($user) {
                $user->update([
                    'google_id' => $googleId ?: $user->google_id,
                    'avatar' => $avatar ?: $user->avatar,
                    'email_verified_at' => $user->email_verified_at ?: now(),
                ]);
            } else {
                $user = User::create([
                    'name' => $name,
                    'email' => $email,
                    'google_id' => $googleId,
                    'avatar' => $avatar,
                    'password' => Hash::make(Str::random(32)),
                    'role' => UserRole::CUSTOMER,
                    'email_verified_at' => now(),
                ]);
            }

            // 4. Login-kan pengguna
            Auth::login($user, true);
            $request->session()->regenerate();

            // 5. Sinkronisasi wishlist tamu ke akun database
            Wishlist::syncGuestSessionToDatabase($user->id);

            return redirect()->intended(route('account.index'))->with(
                'success',
                __('Selamat datang, :name! Anda berhasil masuk menggunakan akun Google.', ['name' => $user->name])
            );
        } catch (\Throwable $e) {
            Log::error('Google OAuth Exception: '.$e->getMessage());

            return redirect()->route('account.index')->with('error', __('Terjadi kendala saat menghubungkan akun Google: ').$e->getMessage());
        }
    }
}
