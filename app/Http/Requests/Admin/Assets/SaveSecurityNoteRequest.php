<?php

namespace App\Http\Requests\Admin\Assets;

use App\Enums\AttachmentType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveSecurityNoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'message' => ['required', 'string'],
            'remove_attachment' => ['nullable', 'boolean'],
            'attachment_type' => ['nullable', Rule::enum(AttachmentType::class)],
            'attachment_file' => ['nullable', 'file', 'max:10240', 'required_if:attachment_type,file'],
            'attachment_link' => ['nullable', 'url', 'max:2048', 'required_if:attachment_type,link'],
        ];
    }

    public function messages(): array
    {
        return [
            'message.required' => 'Catatan tidak boleh kosong.',
            'attachment_type.enum' => 'Tipe lampiran tidak valid.',
            'attachment_file.required_if' => 'File wajib dipilih jika tipe lampiran adalah File.',
            'attachment_file.file' => 'Lampiran harus berupa file yang valid.',
            'attachment_file.max' => 'Ukuran file maksimal 10 MB.',
            'attachment_link.required_if' => 'URL wajib diisi jika tipe lampiran adalah Link.',
            'attachment_link.url' => 'Format URL tidak valid. Pastikan URL diawali dengan http:// atau https://.',
            'attachment_link.max' => 'URL terlalu panjang (maks. 2048 karakter).',
        ];
    }
}
