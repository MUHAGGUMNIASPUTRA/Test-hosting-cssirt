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
            'nip' => ['required', 'string', 'max:30'],
            'nik' => ['required', 'string', 'max:20'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'position_id' => ['required', 'uuid', 'exists:positions,id'],
            'organization_id' => ['required', 'uuid', 'exists:organizations,id'],
            'year_joined' => ['nullable', 'integer', 'min:1900', 'max:2100'],
            'is_active' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'nip.required' => 'NIP wajib diisi.',
            'nik.required' => 'NIK wajib diisi.',
            'position_id.required' => 'Jabatan wajib dipilih.',
            'organization_id.required' => 'Organisasi wajib dipilih.',
        ];
    }
}
