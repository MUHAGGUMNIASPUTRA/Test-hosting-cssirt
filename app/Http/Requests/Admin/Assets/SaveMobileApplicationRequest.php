<?php

namespace App\Http\Requests\Admin\Assets;

use App\Enums\AppStatus;
use App\Enums\AssetStage;
use App\Enums\OwnerContactType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveMobileApplicationRequest extends FormRequest
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
            'stage' => ['required', Rule::enum(AssetStage::class)],
            'app_status' => ['required', Rule::enum(AppStatus::class)],
            'current_version' => ['nullable', 'string', 'max:50'],
            'app_link' => ['nullable', 'url', 'max:2048'],
            'location_id' => ['required', 'uuid', 'exists:locations,id'],
            'provider_org_id' => ['required', 'uuid', 'exists:organizations,id'],
            'owner_org_id' => ['required', 'uuid', 'exists:organizations,id'],
            'owner_contact_type' => ['required', Rule::enum(OwnerContactType::class)],
            'owner_employee_id' => ['nullable', 'uuid', 'exists:employees,id', 'required_if:owner_contact_type,manual'],
            'vendor_id' => ['nullable', 'uuid', 'exists:vendors,id'],

            'tech_stacks' => ['nullable', 'array'],
            'tech_stacks.*.tech_stack_id' => ['required', 'uuid', 'exists:tech_stacks,id'],
            'tech_stacks.*.version' => ['nullable', 'string', 'max:100'],

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
