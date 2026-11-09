<?php

declare(strict_types=1);

namespace App\Http\Requests\Account;

use Illuminate\Foundation\Http\FormRequest;

class UpdateShippingAddressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $action = $this->input('action_type', 'update_primary');

        if (in_array($action, ['set_primary', 'delete_address'], true)) {
            return [
                'action_type' => ['required', 'string', 'in:set_primary,delete_address'],
                'address_id' => ['required', 'string', 'max:100'],
            ];
        }

        return [
            'action_type' => ['nullable', 'string', 'in:update_primary,add_address'],
            'recipient_name' => ['nullable', 'string', 'max:150'],
            'phone' => ['required', 'string', 'max:30'],
            'address_label' => ['required', 'string', 'in:Rumah,Kantor,Apartemen,Kos'],
            'country' => ['required', 'string', 'max:100'],
            'province' => ['required', 'string', 'max:100'],
            'city' => ['required', 'string', 'max:100'],
            'district' => ['required', 'string', 'max:100'],
            'postcode' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string', 'max:500'],
            'address_notes' => ['nullable', 'string', 'max:255'],
            'set_as_primary' => ['nullable', 'boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'phone.required' => 'Nomor telepon penerima wajib diisi.',
            'province.required' => 'Provinsi wajib diisi.',
            'city.required' => 'Kota / Kabupaten wajib diisi.',
            'district.required' => 'Kecamatan wajib diisi.',
            'postcode.required' => 'Kode pos wajib diisi.',
            'address.required' => 'Alamat lengkap (Nama Jalan, No. Rumah, RT/RW) wajib diisi.',
        ];
    }
}
