<?php

namespace App\Http\Requests\Admin\IncidentType;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveIncidentTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $incidentType = $this->route('incidentType');

        return [
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('incident_types', 'name')->ignore($incidentType),
            ],
            'description' => ['nullable', 'string'],
            'guide' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama jenis insiden wajib diisi.',
            'name.unique' => 'Nama jenis insiden sudah digunakan.',
        ];
    }
}
