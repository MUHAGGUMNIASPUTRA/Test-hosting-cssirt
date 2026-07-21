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
            'attachment_file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf,zip,doc,docx', 'max:10240', 'required_if:attachment_type,file'],
            'attachment_link' => ['nullable', 'url', 'max:2048', 'required_if:attachment_type,link'],
        ];
    }

    public function messages(): array
    {
        return [
            'message.required' => 'Catatan audit tidak boleh kosong.',
            'danger_level.required' => 'Tingkat bahaya wajib dipilih.',
            'attachment_type.enum' => 'Tipe lampiran tidak valid.',
            'attachment_file.required_if' => 'File wajib dipilih jika tipe lampiran adalah File.',
            'attachment_file.file' => 'Lampiran harus berupa file yang valid.',
            'attachment_file.mimes' => 'Format file lampiran hanya JPG, PNG, PDF, ZIP, DOC, atau DOCX.',
            'attachment_file.max' => 'Ukuran file maksimal 10 MB.',
            'attachment_link.required_if' => 'URL wajib diisi jika tipe lampiran adalah Link.',
            'attachment_link.url' => 'Format URL tidak valid. Pastikan URL diawali dengan http:// atau https://.',
            'attachment_link.max' => 'URL terlalu panjang (maks. 2048 karakter).',
        ];
    }
}
