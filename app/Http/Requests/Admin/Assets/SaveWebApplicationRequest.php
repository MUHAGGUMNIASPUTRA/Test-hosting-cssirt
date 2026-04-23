<?php

namespace App\Http\Requests\Admin\Assets;

use App\Enums\AppStatus;
use App\Enums\AssetStage;
use App\Enums\HttpsStatus;
use App\Enums\OwnerContactType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveWebApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isDiterima = $this->input('stage') === AssetStage::Diterima->value;

        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'stage' => ['required', Rule::enum(AssetStage::class)],
            'app_status' => ['required', Rule::enum(AppStatus::class)],
            'https_status' => ['required', Rule::enum(HttpsStatus::class)],
            'location_id' => ['required', 'uuid', 'exists:locations,id'],
            'provider_org_id' => ['required', 'uuid', 'exists:organizations,id'],
            'owner_org_id' => ['required', 'uuid', 'exists:organizations,id'],
            'owner_contact_type' => ['required', Rule::enum(OwnerContactType::class)],
            'owner_employee_id' => ['nullable', 'uuid', 'exists:employees,id', 'required_if:owner_contact_type,manual'],
            'vendor_id' => ['nullable', 'uuid', 'exists:vendors,id'],

            'vms' => ['nullable', 'array'],
            'vms.*.processor' => ['nullable', 'string', 'max:255'],
            'vms.*.ram' => ['nullable', 'string', 'max:100'],
            'vms.*.hdd' => ['nullable', 'string', 'max:100'],

            'networks' => [$isDiterima ? 'required' : 'nullable', 'array'],
            'networks.0.ip_address_id' => [$isDiterima ? 'required' : 'nullable', 'uuid', 'exists:ip_addresses,id'],
            'networks.0.environment' => [$isDiterima ? 'required' : 'nullable', 'string', 'max:100'],
            'networks.*.environment' => ['nullable', 'string', 'max:100'],
            'networks.*.ip_address_id' => ['nullable', 'uuid', 'exists:ip_addresses,id'],
            'networks.*.subdomain_id' => ['nullable', 'uuid', 'exists:subdomains,id'],

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
            'networks.required' => 'Jaringan wajib diisi untuk aplikasi yang sudah diterima.',
            'networks.0.ip_address_id.required' => 'IP Address production wajib dipilih untuk aplikasi yang sudah diterima.',
            'networks.0.environment.required' => 'Environment production wajib diisi untuk aplikasi yang sudah diterima.',
        ];
    }
}
