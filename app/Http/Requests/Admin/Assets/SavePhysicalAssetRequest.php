<?php

// Tujuan: Validasi input create/update aset fisik
// Caller: PhysicalAssetController
// Side Effects: none

namespace App\Http\Requests\Admin\Assets;

use App\Enums\OwnerContactType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SavePhysicalAssetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'asset_code' => ['required', 'string', 'max:100'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'specifications' => ['nullable', 'string'],
            'year' => ['nullable', 'integer', 'min:1900', 'max:2100'],
            'attachment_type' => ['nullable', 'in:file,link'],
            'attachment' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf,zip,doc,docx', 'max:5120'],
            'attachment_link' => ['nullable', 'url', 'max:2000'],
            'location_id' => ['nullable', 'uuid', 'exists:locations,id'],
            'owner_org_id' => ['nullable', 'uuid', 'exists:organizations,id'],
            'owner_contact_type' => ['required', Rule::enum(OwnerContactType::class)],
            'owner_employee_id' => ['nullable', 'uuid', 'exists:employees,id', 'required_if:owner_contact_type,manual'],
            'security' => ['nullable', 'array'],
            'security.confidentiality' => ['nullable', 'integer', 'min:1', 'max:5'],
            'security.integrity' => ['nullable', 'integer', 'min:1', 'max:5'],
            'security.availability' => ['nullable', 'integer', 'min:1', 'max:5'],
        ];
    }

    public function messages(): array
    {
        return [
            'asset_code.required' => 'Kode aset wajib diisi.',
            'name.required' => 'Nama aset wajib diisi.',
            'owner_employee_id.required_if' => 'Pegawai PJ wajib dipilih saat kontak manual.',
        ];
    }
}
