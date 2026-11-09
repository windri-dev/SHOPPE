<?php

declare(strict_types=1);

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ConfirmPaymentRequest extends FormRequest
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
        $isManualTransfer = $this->input('action_type', 'manual_transfer') === 'manual_transfer';

        return [
            'action_type' => ['nullable', 'string', 'in:instant_simulate,manual_transfer'],
            'bank_name' => [Rule::requiredIf($isManualTransfer), 'nullable', 'string', 'max:50'],
            'sender_name' => [Rule::requiredIf($isManualTransfer), 'nullable', 'string', 'max:100'],
            'transfer_amount' => [Rule::requiredIf($isManualTransfer), 'nullable', 'numeric', 'min:1'],
            'transfer_notes' => ['nullable', 'string', 'max:255'],
        ];
    }
}
