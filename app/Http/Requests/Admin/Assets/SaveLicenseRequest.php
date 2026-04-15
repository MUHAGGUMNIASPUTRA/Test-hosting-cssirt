<?php

namespace App\Http\Requests\Admin\Assets;

use App\Enums\OwnerContactType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveLicenseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_active' => ['boolean'],
            'version' => ['nullable', 'string', 'max:50'],
            'expired_at' => ['nullable', 'date'],
            'location_id' => ['required', 'uuid', 'exists:locations,id'],
            'provider_org_id' => ['required', 'uuid', 'exists:organizations,id'],
            'owner_org_id' => ['required', 'uuid', 'exists:organizations,id'],
            'owner_contact_type' => ['required', Rule::enum(OwnerContactType::class)],
            'owner_employee_id' => ['nullable', 'uuid', 'exists:employees,id', 'required_if:owner_contact_type,manual'],

            'security' => ['nullable', 'array'],
            'security.confidentiality' => ['nullable', 'integer', 'min:1', 'max:5'],
            'security.integrity' => ['nullable', 'integer', 'min:1', 'max:5'],
            'security.availability' => ['nullable', 'integer', 'min:1', 'max:5'],
            'security.notes' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'location_id.required' => 'Lokasi wajib dipilih.',
            'provider_org_id.required' => 'Penyedia aset wajib dipilih.',
            'owner_org_id.required' => 'Pemilik aset wajib dipilih.',
            'owner_employee_id.required_if' => 'Pegawai penanggung jawab wajib dipilih jika kontak diatur manual.',
        ];
    }
}
