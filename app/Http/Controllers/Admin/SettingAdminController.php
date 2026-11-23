<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\StoreSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingAdminController extends Controller
{
    public function index(Request $request): View
    {
        $banks = BankAccount::ordered()->get();

        $social = [
            'instagram' => StoreSetting::get('social_instagram', 'https://instagram.com/putrijalva.official'),
            'tiktok' => StoreSetting::get('social_tiktok', 'https://tiktok.com/@putrijalva'),
            'whatsapp' => StoreSetting::get('social_whatsapp', '6281234567890'),
            'facebook' => StoreSetting::get('social_facebook', 'https://facebook.com/putrijalva'),
            'youtube' => StoreSetting::get('social_youtube', 'https://youtube.com/@putrijalva'),
            'email' => StoreSetting::get('store_email', 'concierge@wn-cdn.net'),
            'phone' => StoreSetting::get('store_phone', '+62 812-3456-7890'),
            'address' => StoreSetting::get('store_address', 'PUTRIJALVA Atelier Studio, Jl. Senopati No. 42, Kebayoran Baru, Jakarta Selatan'),
        ];

        $activeTab = $request->input('tab', 'banks');

        return view('admin.settings.index', compact('banks', 'social', 'activeTab'));
    }

    public function updateSocial(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'social_instagram' => ['nullable', 'string', 'max:255'],
            'social_tiktok' => ['nullable', 'string', 'max:255'],
            'social_whatsapp' => ['nullable', 'string', 'max:50'],
            'social_facebook' => ['nullable', 'string', 'max:255'],
            'social_youtube' => ['nullable', 'string', 'max:255'],
            'store_email' => ['nullable', 'email', 'max:255'],
            'store_phone' => ['nullable', 'string', 'max:50'],
            'store_address' => ['nullable', 'string', 'max:500'],
        ]);

        foreach ($validated as $key => $val) {
            $group = str_starts_with($key, 'social_') ? 'social' : 'contact';
            StoreSetting::set($key, $val, $group);
        }

        return redirect()->route('admin.settings.index', ['tab' => 'social'])
            ->with('success', 'Pengaturan akun sosial media dan kontak butik berhasil diperbarui!');
    }

    public function storeBank(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'bank_name' => ['required', 'string', 'max:100'],
            'bank_code' => ['nullable', 'string', 'max:50'],
            'account_number' => ['required', 'string', 'max:50'],
            'account_holder' => ['required', 'string', 'max:150'],
            'branch' => ['nullable', 'string', 'max:150'],
            'instructions' => ['nullable', 'string', 'max:500'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['sort_order'] = (int) ($validated['sort_order'] ?? 0);
        $validated['bank_code'] = $request->input('bank_code') ?: strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $validated['bank_name']));

        BankAccount::create($validated);

        return redirect()->route('admin.settings.index', ['tab' => 'banks'])
            ->with('success', "Rekening {$validated['bank_name']} berhasil ditambahkan ke daftar pembayaran butik!");
    }

    public function updateBank(Request $request, BankAccount $bank): RedirectResponse
    {
        $validated = $request->validate([
            'bank_name' => ['required', 'string', 'max:100'],
            'bank_code' => ['nullable', 'string', 'max:50'],
            'account_number' => ['required', 'string', 'max:50'],
            'account_holder' => ['required', 'string', 'max:150'],
            'branch' => ['nullable', 'string', 'max:150'],
            'instructions' => ['nullable', 'string', 'max:500'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        $validated['sort_order'] = (int) ($validated['sort_order'] ?? 0);
        $validated['bank_code'] = $request->input('bank_code') ?: $bank->getAttribute('bank_code') ?: strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $validated['bank_name']));

        $bank->update($validated);

        return redirect()->route('admin.settings.index', ['tab' => 'banks'])
            ->with('success', "Data rekening {$bank->bank_name} berhasil diperbarui!");
    }

    public function toggleBank(BankAccount $bank): RedirectResponse
    {
        $bank->update(['is_active' => ! $bank->is_active]);
        $statusStr = $bank->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Status rekening {$bank->bank_name} berhasil {$statusStr}!");
    }

    public function destroyBank(BankAccount $bank): RedirectResponse
    {
        $name = $bank->bank_name;
        $bank->delete();

        return redirect()->route('admin.settings.index', ['tab' => 'banks'])
            ->with('success', "Rekening {$name} berhasil dihapus dari sistem.");
    }
}
