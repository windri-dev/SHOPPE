<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Account\UpdateBankAccountRequest;
use App\Http\Requests\Account\UpdatePasswordRequest;
use App\Http\Requests\Account\UpdateProfileRequest;
use App\Http\Requests\Account\UpdateShippingAddressRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use App\Services\Auth\RoleRedirectService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AccountController extends Controller
{
    public function __construct(
        protected RoleRedirectService $roleRedirectService
    ) {}

    public function index(): View|RedirectResponse
    {
        if (Auth::check() && Auth::user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        $user = Auth::user();
        $orders = $user ? $user->orders()->with('items')->latest()->get() : collect();

        return view('pages.account', compact('user', 'orders'));
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();

            return $this->roleRedirectService->redirectBasedOnRole($user);
        }

        return back()->withErrors([
            'email' => 'Email atau kata sandi yang Anda masukkan salah.',
        ])->onlyInput('email');
    }

    public function register(RegisterRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $username = ! empty($validated['username'])
            ? $validated['username']
            : Str::slug($validated['name'], '.').'.'.rand(10, 99);

        $savedAddresses = [];
        if (! empty($validated['address'])) {
            $savedAddresses[] = [
                'id' => 'addr-'.uniqid(),
                'label' => $validated['address_label'] ?? 'Rumah',
                'recipient_name' => $validated['name'],
                'phone' => $validated['phone'] ?? '',
                'country' => $validated['country'] ?? 'Indonesia',
                'province' => $validated['province'] ?? '',
                'city' => $validated['city'] ?? '',
                'district' => $validated['district'] ?? '',
                'postcode' => $validated['postcode'] ?? '',
                'street_address' => $validated['address'],
                'notes' => $validated['address_notes'] ?? '',
                'is_primary' => true,
            ];
        }

        $user = User::create([
            'name' => $validated['name'],
            'username' => $username,
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'country' => $validated['country'] ?? 'Indonesia',
            'province' => $validated['province'] ?? null,
            'city' => $validated['city'] ?? null,
            'district' => $validated['district'] ?? null,
            'postcode' => $validated['postcode'] ?? null,
            'address' => $validated['address'] ?? null,
            'address_label' => $validated['address_label'] ?? 'Rumah',
            'address_notes' => $validated['address_notes'] ?? null,
            'saved_addresses' => count($savedAddresses) > 0 ? $savedAddresses : null,
            'notification_preferences' => [
                'email_order' => true,
                'whatsapp_shipping' => true,
                'promo_newsletter' => true,
            ],
            'password' => Hash::make($validated['password']),
            'role' => UserRole::CUSTOMER,
        ]);

        Auth::login($user);

        return $this->roleRedirectService->redirectBasedOnRole($user);
    }

    public function updateProfile(UpdateProfileRequest $request): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();
        $validated = $request->validated();

        $user->update([
            'name' => $validated['name'],
            'username' => $validated['username'] ?? $user->username,
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'gender' => $validated['gender'] ?? $user->gender,
            'birth_date' => $validated['birth_date'] ?? $user->birth_date,
            'notification_preferences' => [
                'email_order' => $request->boolean('email_order'),
                'whatsapp_shipping' => $request->boolean('whatsapp_shipping'),
                'promo_newsletter' => $request->boolean('promo_newsletter'),
            ],
        ]);

        return back()->with('success', 'Profil & informasi kontak Anda berhasil diperbarui.')->with('active_tab', 'profile');
    }

    public function updateShippingAddress(UpdateShippingAddressRequest $request): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();
        $validated = $request->validated();
        $action = $validated['action_type'] ?? 'update_primary';
        $addresses = $user->address_book;

        if ($action === 'set_primary') {
            $targetId = $validated['address_id'];
            $primaryAddr = null;

            foreach ($addresses as &$addr) {
                if (($addr['id'] ?? '') === $targetId) {
                    $addr['is_primary'] = true;
                    $primaryAddr = $addr;
                } else {
                    $addr['is_primary'] = false;
                }
            }
            unset($addr);

            if ($primaryAddr) {
                $user->update([
                    'phone' => $primaryAddr['phone'] ?: $user->phone,
                    'country' => $primaryAddr['country'] ?? 'Indonesia',
                    'province' => $primaryAddr['province'] ?? '',
                    'city' => $primaryAddr['city'] ?? '',
                    'district' => $primaryAddr['district'] ?? '',
                    'postcode' => $primaryAddr['postcode'] ?? '',
                    'address' => $primaryAddr['street_address'] ?? '',
                    'address_label' => $primaryAddr['label'] ?? 'Rumah',
                    'address_notes' => $primaryAddr['notes'] ?? '',
                    'saved_addresses' => array_values($addresses),
                ]);
            }

            return back()->with('success', 'Alamat pengiriman utama berhasil diubah.')->with('active_tab', 'addresses');
        }

        if ($action === 'delete_address') {
            $targetId = $validated['address_id'];
            $filtered = array_values(array_filter($addresses, fn (array $a): bool => ($a['id'] ?? '') !== $targetId));
            $user->update(['saved_addresses' => $filtered]);

            return back()->with('success', 'Alamat berhasil dihapus dari buku alamat.')->with('active_tab', 'addresses');
        }

        if ($action === 'add_address') {
            $isPrimary = $request->boolean('set_as_primary') || count($addresses) === 0;

            if ($isPrimary) {
                foreach ($addresses as &$addr) {
                    $addr['is_primary'] = false;
                }
                unset($addr);
            }

            $newEntry = [
                'id' => 'addr-'.uniqid(),
                'label' => $validated['address_label'],
                'recipient_name' => $validated['recipient_name'] ?: $user->name,
                'phone' => $validated['phone'],
                'country' => $validated['country'],
                'province' => $validated['province'],
                'city' => $validated['city'],
                'district' => $validated['district'],
                'postcode' => $validated['postcode'],
                'street_address' => $validated['address'],
                'notes' => $validated['address_notes'] ?? '',
                'is_primary' => $isPrimary,
            ];

            array_unshift($addresses, $newEntry);

            $updates = ['saved_addresses' => array_values($addresses)];
            if ($isPrimary) {
                $updates = array_merge($updates, [
                    'phone' => $validated['phone'],
                    'country' => $validated['country'],
                    'province' => $validated['province'],
                    'city' => $validated['city'],
                    'district' => $validated['district'],
                    'postcode' => $validated['postcode'],
                    'address' => $validated['address'],
                    'address_label' => $validated['address_label'],
                    'address_notes' => $validated['address_notes'] ?? null,
                ]);
            }

            $user->update($updates);

            return back()->with('success', 'Alamat pengiriman baru berhasil ditambahkan ke Buku Alamat.')->with('active_tab', 'addresses');
        }

        // Default: update_primary
        $updatedPrimary = false;
        foreach ($addresses as &$addr) {
            if (! empty($addr['is_primary'])) {
                $addr['label'] = $validated['address_label'];
                $addr['recipient_name'] = $validated['recipient_name'] ?: $user->name;
                $addr['phone'] = $validated['phone'];
                $addr['country'] = $validated['country'];
                $addr['province'] = $validated['province'];
                $addr['city'] = $validated['city'];
                $addr['district'] = $validated['district'];
                $addr['postcode'] = $validated['postcode'];
                $addr['street_address'] = $validated['address'];
                $addr['notes'] = $validated['address_notes'] ?? '';
                $updatedPrimary = true;
                break;
            }
        }
        unset($addr);

        if (! $updatedPrimary) {
            array_unshift($addresses, [
                'id' => 'addr-'.uniqid(),
                'label' => $validated['address_label'],
                'recipient_name' => $validated['recipient_name'] ?: $user->name,
                'phone' => $validated['phone'],
                'country' => $validated['country'],
                'province' => $validated['province'],
                'city' => $validated['city'],
                'district' => $validated['district'],
                'postcode' => $validated['postcode'],
                'street_address' => $validated['address'],
                'notes' => $validated['address_notes'] ?? '',
                'is_primary' => true,
            ]);
        }

        $user->update([
            'phone' => $validated['phone'],
            'country' => $validated['country'],
            'province' => $validated['province'],
            'city' => $validated['city'],
            'district' => $validated['district'],
            'postcode' => $validated['postcode'],
            'address' => $validated['address'],
            'address_label' => $validated['address_label'],
            'address_notes' => $validated['address_notes'] ?? null,
            'saved_addresses' => array_values($addresses),
        ]);

        return back()->with('success', 'Alamat pengiriman utama berhasil disimpan dan siap digunakan saat Checkout.')->with('active_tab', 'addresses');
    }

    public function updateBankAccount(UpdateBankAccountRequest $request): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();
        $validated = $request->validated();

        $user->update([
            'bank_account' => [
                'bank_name' => $validated['bank_name'],
                'account_number' => $validated['account_number'],
                'account_holder' => $validated['account_holder'],
            ],
        ]);

        return back()->with('success', 'Rekening bank utama berhasil disimpan.')->with('active_tab', 'bank');
    }

    public function updatePassword(UpdatePasswordRequest $request): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();
        $validated = $request->validated();

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('success', 'Kata sandi akun Anda berhasil diperbarui.')->with('active_tab', 'security');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Anda telah keluar dari akun.');
    }
}
