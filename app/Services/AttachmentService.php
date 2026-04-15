<?php

namespace App\Services;

use App\Enums\AttachmentType;
use App\Models\Attachment;
use Illuminate\Http\UploadedFile;

class AttachmentService
{
    /**
     * Store an uploaded file and create an Attachment record.
     */
    public function storeFile(UploadedFile $file, string $disk, string $directory): Attachment
    {
        $path = $file->store($directory, $disk);

        return Attachment::create([
            'type' => AttachmentType::File,
            'disk' => $disk,
            'path' => $path,
            'filename' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
        ]);
    }

    /**
     * Create an Attachment record for an external URL.
     */
    public function storeLink(string $url): Attachment
    {
        return Attachment::create([
            'type' => AttachmentType::Link,
            'url' => $url,
            'filename' => basename(parse_url($url, PHP_URL_PATH) ?: $url),
        ]);
    }

    /**
     * Resolve an attachment from form input, replacing the existing one if needed.
     *
     * Rules:
     * - type='file' + new file uploaded → delete old, store new file
     * - type='file' + no new file → keep existing (return as-is)
     * - type='link' + link provided → delete old file, store new link
     * - type='link' + no link → keep existing
     * - type=null or anything else → keep existing
     *
     * @param  string|null  $linkValue  The submitted URL string (for link mode)
     */
    public function resolve(
        ?UploadedFile $file,
        ?string $type,
        ?string $linkValue,
        ?Attachment $existing,
        string $disk,
        string $directory,
    ): ?Attachment {
        if ($type === AttachmentType::File->value) {
            if ($file !== null) {
                $existing?->delete();

                return $this->storeFile($file, $disk, $directory);
            }

            // No new file — preserve existing
            return $existing;
        }

        if ($type === AttachmentType::Link->value) {
            if (! empty($linkValue)) {
                $existing?->delete();

                return $this->storeLink($linkValue);
            }

            // No link provided — preserve existing
            return $existing;
        }

        return $existing;
    }

    /**
     * Delete an attachment record (and its file if applicable).
     * Safe to call with null.
     */
    public function delete(?Attachment $attachment): void
    {
        $attachment?->delete();
    }
}
