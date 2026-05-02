<?php

namespace App\Http\Requests\Admin\Assets;

use Illuminate\Foundation\Http\FormRequest;

class SaveOrganizationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'it_contact_name' => ['nullable', 'string', 'max:255'],
            'it_contact_phone' => ['nullable', 'string', 'max:50'],
            'it_contact_email' => ['nullable', 'email', 'max:255'],
        ];
    }
}
