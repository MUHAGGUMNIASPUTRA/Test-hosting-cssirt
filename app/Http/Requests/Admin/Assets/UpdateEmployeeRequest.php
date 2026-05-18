<?php

namespace App\Http\Requests\Admin\Assets;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            // Sensitive fields nullable on update — empty means "keep existing"
            'nip' => ['nullable', 'string', 'max:30'],
            'nik' => ['nullable', 'string', 'max:20'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'position_id' => ['required', 'uuid', 'exists:positions,id'],
            'year_joined' => ['nullable', 'integer', 'min:1900', 'max:2100'],
            'is_active' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'position_id.required' => 'Jabatan wajib dipilih.',
        ];
    }
}
