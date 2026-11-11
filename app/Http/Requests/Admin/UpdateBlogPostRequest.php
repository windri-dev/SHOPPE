<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBlogPostRequest extends FormRequest
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
            'category' => ['required', 'string', 'max:100'],
            'author' => ['required', 'string', 'max:100'],
            'excerpt' => ['required', 'string', 'max:500'],
            'excerpt_en' => ['nullable', 'string', 'max:500'],
            'content' => ['required', 'string'],
            'content_en' => ['nullable', 'string'],
            'image' => ['nullable', 'string', 'max:255'],
        ];
    }
}
