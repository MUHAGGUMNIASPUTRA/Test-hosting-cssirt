<?php

namespace App\Http\Requests\Admin\Document;

use App\Enums\DocumentStage;

class UpdateDocumentRequest extends StoreDocumentRequest
{
    public function rules(): array
    {
        $rules = parent::rules();

        // Saat edit: official_file hanya wajib jika stage Final, mode file,
        // DAN belum ada file tersimpan di DB (officialAttachment bertipe file).
        $document = $this->route('document');
        $hasExistingFile = $document?->officialAttachment?->isFile();

        $isFinalFileMode = $this->input('stage') === DocumentStage::Final->value
            && $this->input('official_file_type') === 'file';

        $rules['official_file'] = [
            $isFinalFileMode && ! $hasExistingFile ? 'required' : 'nullable',
            'file',
            'mimes:pdf',
            'max:51200',
        ];

        return $rules;
    }
}
