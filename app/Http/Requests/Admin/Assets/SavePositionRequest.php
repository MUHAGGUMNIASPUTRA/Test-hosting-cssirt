<?php

namespace App\Http\Requests\Admin\Assets;

use Illuminate\Foundation\Http\FormRequest;

class SavePositionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'department_id' => ['required', 'uuid', 'exists:departments,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'department_id.required' => 'Bidang wajib dipilih.',
            'department_id.exists' => 'Bidang tidak ditemukan.',
        ];
    }
}
