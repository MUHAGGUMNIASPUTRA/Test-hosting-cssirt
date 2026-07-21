<?php

namespace App\Http\Requests\Admin\Incident;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLogRequest extends FormRequest
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
            'attachment_type' => ['nullable', 'string', 'in:file,link,none'],
            'attachment' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf,zip,doc,docx', 'max:5120', 'required_if:attachment_type,file'],
            'attachment_link' => ['nullable', 'string', 'url', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'attachment.required_if' => 'File wajib dipilih jika tipe lampiran adalah File.',
            'attachment.file' => 'Lampiran harus berupa file yang valid.',
            'attachment.mimes' => 'Format file lampiran hanya JPG, PNG, PDF, ZIP, DOC, atau DOCX.',
            'attachment.max' => 'Ukuran file maksimal 5 MB.',
        ];
    }
}
