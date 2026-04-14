<?php

namespace App\Http\Requests\Admin\Document;

use App\Enums\DocumentStage;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'version' => ['nullable', 'string', 'max:50'],
            'published_at' => ['nullable', 'date'],
            'is_public' => ['boolean'],
            'document_area_id' => ['nullable', 'exists:document_areas,id'],
            'reference_number' => [
                $this->input('stage') === DocumentStage::Final->value ? 'required' : 'nullable',
                'string',
                'max:100',
            ],
            'stage' => ['nullable', Rule::enum(DocumentStage::class)],
            'doc_file_link' => ['nullable', 'url', 'max:2000'],
            'official_file_type' => ['required', 'in:file,link'],
            'official_file' => [
                $this->input('stage') === DocumentStage::Final->value && $this->input('official_file_type') === 'file' ? 'required' : 'nullable',
                'file',
                'mimes:pdf',
                'max:51200',
            ],
            'official_file_link' => [
                $this->input('stage') === DocumentStage::Final->value && $this->input('official_file_type') === 'link' ? 'required' : 'nullable',
                'url',
                'max:2000',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'official_file_type.required' => 'Jenis File Dokumen Sah wajib dipilih.',
            'official_file.mimes' => 'File Dokumen Sah harus berupa PDF.',
            'official_file.max' => 'Ukuran File Dokumen Sah maksimal 50MB.',
            'doc_file_link.url' => 'Link File Dokumen harus berupa URL yang valid.',
            'official_file_link.url' => 'Link File Dokumen Sah harus berupa URL yang valid (satu URL saja).',
            'official_file_link.required' => 'Link File Dokumen Sah wajib diisi ketika stage Final.',
            'stage.Illuminate\Validation\Rules\Enum' => 'Pilihan stage tidak valid.',
            'reference_number.required' => 'Nomor Referensi wajib diisi ketika stage Final.',
            'official_file.required' => 'File PDF Dokumen Sah wajib diupload ketika stage Final.',
        ];
    }
}
