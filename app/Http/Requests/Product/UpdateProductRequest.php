<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('colors') && is_string($this->colors)) {
            $this->merge([
                'colors' => array_values(array_filter(array_map('trim', explode(',', $this->colors)))),
            ]);
        }
        if ($this->has('available_sizes') && is_string($this->available_sizes)) {
            $this->merge([
                'available_sizes' => array_values(array_filter(array_map('trim', explode(',', $this->available_sizes)))),
            ]);
        }
        if ($this->has('tags') && is_string($this->tags)) {
            $this->merge([
                'tags' => array_values(array_filter(array_map('trim', explode(',', $this->tags)))),
            ]);
        }
        if ($this->has('show_price')) {
            $this->merge(['show_price' => filter_var($this->show_price, FILTER_VALIDATE_BOOLEAN)]);
        }
        if ($this->has('replace_images')) {
            $this->merge(['replace_images' => filter_var($this->replace_images, FILTER_VALIDATE_BOOLEAN)]);
        }
    }

    public function rules(): array
    {
        $productId = $this->route('product')?->id ?? $this->route('id');

        return [
            'name' => ['sometimes', 'required', 'string', 'max:200'],
            'description' => ['nullable', 'string'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'category_name' => ['nullable', 'string', 'max:120'],
            'designer' => ['nullable', 'string', 'max:120'],
            'fabric' => ['nullable', 'string', 'max:120'],
            'fit' => ['nullable', 'string', 'max:120'],
            'colors' => ['nullable', 'array'],
            'colors.*' => ['string', 'max:60'],
            'available_sizes' => ['nullable', 'array'],
            'available_sizes.*' => ['string', 'max:40'],
            'show_price' => ['sometimes', 'boolean'],
            'price' => ['sometimes', 'required', 'numeric', 'min:0'],
            'sku' => ['nullable', 'string', 'max:80', Rule::unique('products', 'sku')->ignore($productId)],
            'care_instructions' => ['nullable', 'string'],
            'admin_note' => ['nullable', 'string', 'max:5000'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['string', 'max:60'],
            'status' => ['nullable', Rule::in(['active', 'draft', 'archived'])],
            'images' => ['nullable', 'array', 'max:10'],
            'images.*' => ['image', 'max:4096'],
            'remove_image_ids' => ['nullable', 'array'],
            'remove_image_ids.*' => ['integer', 'exists:product_images,id'],
            'primary_image_id' => ['nullable', 'integer', 'exists:product_images,id'],
            'replace_images' => ['sometimes', 'boolean'],
        ];
    }
}
