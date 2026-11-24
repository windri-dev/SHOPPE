<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBannerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null && $this->user()->isAdmin();
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'title_en' => ['nullable', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'subtitle_en' => ['nullable', 'string', 'max:255'],
            'button_text' => ['nullable', 'string', 'max:100'],
            'button_text_en' => ['nullable', 'string', 'max:100'],
            'button_link' => ['required', 'string', 'max:500'],
            'image' => ['nullable', 'file', 'image', 'mimes:jpeg,png,jpg,webp,gif', 'max:10240'],
            'image_url' => ['nullable', 'string', 'max:500'],
            'badge_text' => ['nullable', 'string', 'max:100'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
