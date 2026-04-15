<?php

namespace App\Http\Requests\Admin\Assets;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'nip' => ['nullable', 'string', 'max:30'],
            'nik' => ['nullable', 'string', 'max:20'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'position_id' => ['nullable', 'uuid', 'exists:positions,id'],
            'organization_id' => ['nullable', 'uuid', 'exists:organizations,id'],
            'year_joined' => ['nullable', 'integer', 'min:1900', 'max:2100'],
            'is_active' => ['boolean'],
        ];
    }
}
