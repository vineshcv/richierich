<?php

namespace App\Http\Requests\Setting;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        foreach (['show_share', 'show_whatsapp', 'show_cart', 'show_view'] as $key) {
            if ($this->has($key)) {
                $this->merge([$key => filter_var($this->input($key), FILTER_VALIDATE_BOOLEAN)]);
            }
        }
    }

    public function rules(): array
    {
        return [
            'show_share' => ['sometimes', 'boolean'],
            'show_whatsapp' => ['sometimes', 'boolean'],
            'show_cart' => ['sometimes', 'boolean'],
            'show_view' => ['sometimes', 'boolean'],
        ];
    }
}
