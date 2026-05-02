<?php

namespace App\Http\Requests\Admin\Assets;

use Illuminate\Foundation\Http\FormRequest;

class SaveDepartmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'organization_id' => ['required', 'uuid', 'exists:organizations,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'organization_id.required' => 'Organisasi wajib dipilih.',
            'organization_id.exists' => 'Organisasi tidak ditemukan.',
        ];
    }
}
