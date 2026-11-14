<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
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
        /** @var Product|null $product */
        $product = $this->route('product');

        return [
            'name' => ['required', 'string', 'max:255'],
            'name_en' => ['nullable', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'sku' => ['required', 'string', 'max:50', Rule::unique('products', 'sku')->ignore($product?->id)],
            'price' => ['required', 'numeric', 'min:0.01'],
            'original_price' => ['nullable', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'size_stocks' => ['nullable', 'array'],
            'size_stocks.*' => ['integer', 'min:0'],
            'badge' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:65000'],
            'description_en' => ['nullable', 'string', 'max:65000'],
            'image_url' => ['nullable', 'string', 'max:500'],
            'image' => ['nullable', 'file', 'image', 'mimes:jpeg,png,jpg,webp,gif', 'max:10240'],
            'images' => ['nullable', 'array'],
            'images.*' => ['file', 'image', 'mimes:jpeg,png,jpg,webp,gif', 'max:10240'],
            'video_url' => ['nullable', 'string', 'max:500'],
            'is_featured' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
