<?php

namespace App\Http\Requests\Admin\Assets;

use App\Enums\AttachmentType;
use App\Enums\AuditDangerLevel;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveAuditLogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'message' => ['required', 'string'],
            'danger_level' => ['required', Rule::enum(AuditDangerLevel::class)],
            'attachment_type' => ['nullable', Rule::enum(AttachmentType::class)],
            'attachment_file' => ['nullable', 'file', 'max:10240', 'required_if:attachment_type,file'],
            'attachment_link' => ['nullable', 'url', 'max:2048', 'required_if:attachment_type,link'],
        ];
    }
}
