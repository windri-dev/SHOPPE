<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ReplyInquiryRequest extends FormRequest
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
            'message' => ['required', 'string', 'max:2000'],
            'save_as_knowledge' => ['nullable', 'boolean'],
            'knowledge_title' => ['nullable', 'string', 'max:150'],
            'knowledge_keywords' => ['nullable', 'string', 'max:500'],
        ];
    }
}
