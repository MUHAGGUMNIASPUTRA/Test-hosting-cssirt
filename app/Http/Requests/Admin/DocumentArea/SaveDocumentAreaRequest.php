<?php

namespace App\Http\Requests\Admin\DocumentArea;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveDocumentAreaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $documentArea = $this->route('documentArea');

        return [
            'name'        => [
                'required', 'string', 'max:255',
                Rule::unique('document_areas', 'name')->ignore($documentArea),
            ],
            'description' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama area dokumen wajib diisi.',
            'name.unique'   => 'Nama area dokumen sudah digunakan.',
        ];
    }
}
