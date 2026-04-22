<?php

// Tujuan: Validasi input create/update alamat IP
// Caller: IpAddressController
// Side Effects: none

namespace App\Http\Requests\Admin\Assets;

use Illuminate\Foundation\Http\FormRequest;

class SaveIpAddressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'private_ip' => ['required', 'ip'],
            'public_ip' => ['nullable', 'ip'],
            'description' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'private_ip.required' => 'IP private wajib diisi.',
            'private_ip.ip' => 'IP private harus berupa alamat IP yang valid.',
            'public_ip.ip' => 'IP publik harus berupa alamat IP yang valid.',
        ];
    }
}
