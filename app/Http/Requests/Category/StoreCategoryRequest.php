<?php

namespace App\Http\Requests\Category;

use App\Models\Category;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('is_active')) {
            $this->merge(['is_active' => filter_var($this->is_active, FILTER_VALIDATE_BOOLEAN)]);
        }
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120', Rule::unique('categories', 'name'), $this->notAlreadyNamed()],
            'description' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'max:2048'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique' => 'This category already exists. Add your products under it.',
        ];
    }

    private function notAlreadyNamed(): \Closure
    {
        return function (string $attribute, mixed $value, \Closure $fail): void {
            $name = mb_strtolower(trim((string) $value));
            $taken = Category::query()->whereRaw('LOWER(name) = ?', [$name])->exists();
            if ($taken) {
                $fail('This category already exists. Add your products under it.');
            }
        };
    }
}
