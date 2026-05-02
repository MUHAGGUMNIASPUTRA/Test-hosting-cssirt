<?php

namespace App\Http\Resources;

use App\Models\Attachment;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Unified JSON shape for any Attachment model.
 *
 * File:
 *   { type: 'file', filename, extension, file_size, url }
 *   url = /storage/{path} for public disk; null for local disk (caller adds signed URL separately)
 *
 * Link:
 *   { type: 'link', filename, url, extension: null, file_size: null }
 *
 * @mixin Attachment
 */
class AttachmentResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'type' => $this->type->value,
            'filename' => $this->filename,
            'extension' => $this->isFile()
                ? strtoupper(pathinfo($this->filename ?? '', PATHINFO_EXTENSION)) ?: null
                : null,
            'file_size' => $this->formattedFileSize(),
            'url' => $this->displayUrl(),
        ];
    }
}
