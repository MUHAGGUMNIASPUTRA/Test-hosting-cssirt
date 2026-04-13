<?php

namespace App\Http\Requests\Admin\Service;

use Illuminate\Foundation\Http\FormRequest;

class SaveServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'              => ['required', 'string', 'max:255'],
            'icon'              => ['nullable', 'string', 'max:255'],
            'short_description' => ['required', 'string', 'max:500'],
            'full_description'  => ['nullable', 'string'],
            'is_active'         => ['boolean'],
        ];
    }
}
