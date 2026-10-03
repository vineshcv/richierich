<?php

namespace App\Http\Requests\Product;

use App\Models\Category;
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
        $sku = trim((string) $this->input('sku', ''));
        $this->merge(['sku' => $sku === '' ? null : $sku]);
        $this->attachExistingCategory();
    }

    private function attachExistingCategory(): void
    {
        $name = trim((string) $this->input('category_name', ''));
        $typed = trim((string) $this->input('category_search', $name));
        if ($typed !== '') {
            $name = $typed;
        }
        if ($name === '') {
            return;
        }

        $existing = Category::query()
            ->whereRaw('LOWER(name) = ?', [mb_strtolower($name)])
            ->first();

        if ($existing) {
            $this->merge([
                'category_id' => $existing->id,
                'category_name' => null,
            ]);

            return;
        }

        $this->merge([
            'category_id' => null,
            'category_name' => $name,
        ]);
    }

    public function rules(): array
    {
        return [
            'store_id' => $this->user()?->role === 'superadmin'
                ? ['nullable', 'integer', 'exists:stores,id']
                : ['prohibited'],
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
            'price' => ['required', 'numeric', 'min:0', 'regex:/^\d+(\.\d{1,2})?$/'],
            'stock' => ['required', 'integer', 'min:0', 'max:100000', 'regex:/^\d+$/'],
            'stock_threshold' => ['nullable', 'integer', 'min:0', 'max:100000'],
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

    public function messages(): array
    {
        return [
            'category_id.required_without' => 'Choose a category.',
            'category_name.required_without' => 'Choose a category.',
            'sku.unique' => 'This SKU is already used.',
            'price.regex' => 'Enter the price using numbers only.',
            'stock.regex' => 'Enter the stock using whole numbers only.',
        ];
    }
}
