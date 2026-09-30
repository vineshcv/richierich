<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductRequest extends FormRequest
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
        // Allow creating category by name in same request
        if ($this->filled('category_name') && ! $this->filled('category_id')) {
            // handled in service
        }
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id', 'required_without:category_name'],
            'category_name' => ['nullable', 'string', 'max:120', 'required_without:category_id'],
            'designer' => ['nullable', 'string', 'max:120'],
            'fabric' => ['nullable', 'string', 'max:120'],
            'fit' => ['nullable', 'string', 'max:120'],
            'colors' => ['nullable', 'array'],
            'colors.*' => ['string', 'max:60'],
            'available_sizes' => ['nullable', 'array'],
            'available_sizes.*' => ['string', 'max:40'],
            'show_price' => ['sometimes', 'boolean'],
            'price' => ['required', 'numeric', 'min:0'],
            'sku' => ['nullable', 'string', 'max:80', Rule::unique('products', 'sku')],
            'care_instructions' => ['nullable', 'string'],
            'admin_note' => ['nullable', 'string', 'max:5000'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['string', 'max:60'],
            'status' => ['nullable', Rule::in(['active', 'draft', 'archived'])],
            'images' => ['nullable', 'array', 'max:10'],
            'images.*' => ['image', 'max:4096'],
        ];
    }
}
