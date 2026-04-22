<?php

// Tujuan: Validasi input create/update aset informasi
// Caller: InformationAssetController
// Side Effects: none

namespace App\Http\Requests\Admin\Assets;

use Illuminate\Foundation\Http\FormRequest;

class SaveInformationAssetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'document_id' => ['nullable', 'uuid', 'exists:documents,id'],
            'storage_format' => ['required', 'in:file_dokumen,cetak,keduanya'],
            'location_id' => ['nullable', 'uuid', 'exists:locations,id'],
            'owner_org_id' => ['nullable', 'uuid', 'exists:organizations,id'],
            'security' => ['nullable', 'array'],
            'security.confidentiality' => ['nullable', 'integer', 'min:1', 'max:5'],
            'security.integrity' => ['nullable', 'integer', 'min:1', 'max:5'],
            'security.availability' => ['nullable', 'integer', 'min:1', 'max:5'],
        ];
    }

    public function messages(): array
    {
        return [
            'storage_format.required' => 'Format penyimpanan wajib dipilih.',
            'storage_format.in' => 'Format penyimpanan tidak valid.',
        ];
    }
}
