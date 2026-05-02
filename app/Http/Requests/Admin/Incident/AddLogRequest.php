<?php

namespace App\Http\Requests\Admin\Incident;

use Illuminate\Foundation\Http\FormRequest;

class AddLogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'log_message' => ['required', 'string'],
            'is_public' => ['nullable', 'boolean'],
            'attachment_type' => ['nullable', 'string', 'in:file,link'],
            'attachment' => ['nullable', 'file', 'max:5120'],
            'attachment_link' => ['nullable', 'string', 'url', 'max:2000'],
        ];
    }
}
