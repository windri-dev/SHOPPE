<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null && $this->user()->isAdmin();
    }

    protected function prepareForValidation(): void
    {
        $mergeData = [];

        if ($this->has('price')) {
            $rawPrice = (string) $this->input('price');
            // Bersihkan titik ribuan dan simbol Rp jika ada
            $cleaned = preg_replace('/[^0-9.]/', '', $rawPrice) ?? '';
            if (substr_count($cleaned, '.') > 1 || (strlen($cleaned) > 6 && str_contains($cleaned, '.'))) {
                $cleaned = str_replace('.', '', $cleaned);
            }
            $mergeData['price'] = $cleaned !== '' ? (float) $cleaned : null;
        }

        if ($this->has('original_price')) {
            $rawOrig = (string) $this->input('original_price');
            $cleaned = preg_replace('/[^0-9.]/', '', $rawOrig) ?? '';
            if (substr_count($cleaned, '.') > 1 || (strlen($cleaned) > 6 && str_contains($cleaned, '.'))) {
                $cleaned = str_replace('.', '', $cleaned);
            }
            $mergeData['original_price'] = $cleaned !== '' ? (float) $cleaned : null;
        }

        if (! empty($mergeData)) {
            $this->merge($mergeData);
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'name_en' => ['nullable', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'sku' => ['nullable', 'string', 'max:50', 'unique:products,sku'],
            'price' => ['required', 'numeric', 'min:0.01'],
            'original_price' => ['nullable', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'size_stocks' => ['nullable', 'array'],
            'size_stocks.*' => ['integer', 'min:0'],
            'badge' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:65000'],
            'description_en' => ['nullable', 'string', 'max:65000'],
            'image_url' => ['nullable', 'string', 'max:500'],
            'image' => ['nullable', 'file', 'image', 'mimes:jpeg,png,jpg,webp,gif', 'max:10240'], // 10MB
            'images' => ['nullable', 'array'],
            'images.*' => ['file', 'image', 'mimes:jpeg,png,jpg,webp,gif', 'max:10240'],
            'video' => ['nullable', 'file', 'mimetypes:video/mp4,video/quicktime,video/webm,video/x-msvideo', 'max:51200'], // 50MB
            'video_url' => ['nullable', 'string', 'max:500'],
            'is_featured' => ['nullable', 'boolean'],
        ];
    }
}
