<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'customer_name' => ['required', 'string', 'max:100'],
            'customer_email' => ['nullable', 'email', 'max:150'],
            'size_purchased' => ['nullable', 'string', 'max:100'],
            'headline' => ['nullable', 'string', 'max:150'],
            'quality_tag' => ['nullable', 'string', 'max:100'],
            'fit_tag' => ['nullable', 'string', 'max:100'],
            'comment' => ['required', 'string', 'min:5', 'max:2000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'rating.required' => 'Silakan pilih bintang rating (1 - 5).',
            'rating.min' => 'Rating minimal 1 bintang.',
            'rating.max' => 'Rating maksimal 5 bintang.',
            'customer_name.required' => 'Nama lengkap wajib diisi.',
            'customer_email.email' => 'Format email tidak valid.',
            'comment.required' => 'Ulasan atau ulasan produk wajib diisi.',
            'comment.min' => 'Ulasan minimal terdiri dari 5 karakter.',
        ];
    }
}
