<?php

declare(strict_types=1);

namespace App\Http\Requests\Checkout;

use Illuminate\Foundation\Http\FormRequest;

class ProcessCheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150'],
            'phone' => ['nullable', 'string', 'max:30'],
            'country' => ['required', 'string', 'max:100'],
            'province' => ['nullable', 'string', 'max:100'],
            'city' => ['required', 'string', 'max:100'],
            'district' => ['nullable', 'string', 'max:100'],
            'postcode' => ['required', 'string', 'max:20'],
            'street_address' => ['required', 'string', 'max:255'],
            'address_label' => ['nullable', 'string', 'max:30'],
            'save_to_profile' => ['nullable', 'boolean'],
            'payment_method' => ['required', 'string', 'in:bank_transfer,qris,credit_card,cod'],
            'shipping_cost' => ['nullable', 'numeric', 'min:0'],
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
            'order_notes' => ['nullable', 'string', 'max:1000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.id' => ['required', 'integer', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'first_name.required' => 'Nama depan wajib diisi.',
            'last_name.required' => 'Nama belakang wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'street_address.required' => 'Alamat pengiriman wajib diisi.',
            'city.required' => 'Kota pengiriman wajib diisi.',
            'postcode.required' => 'Kode pos wajib diisi.',
            'items.required' => 'Keranjang belanja Anda masih kosong.',
            'items.min' => 'Pilih minimal 1 produk untuk melanjutkan checkout.',
            'items.*.id.exists' => 'Salah satu produk di keranjang tidak ditemukan di katalog.',
        ];
    }
}
