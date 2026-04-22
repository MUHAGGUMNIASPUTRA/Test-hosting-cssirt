<?php

// Tujuan: Validasi input create/update subdomain
// Caller: SubdomainController
// Side Effects: none

namespace App\Http\Requests\Admin\Assets;

use Illuminate\Foundation\Http\FormRequest;

class SaveSubdomainRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'subdomain' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'subdomain.required' => 'Subdomain wajib diisi.',
        ];
    }
}
