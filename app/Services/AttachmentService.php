<?php

namespace App\Services;

use App\Enums\AttachmentType;
use App\Models\Attachment;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class AttachmentService
{
    /**
     * Store an uploaded file with image normalization (re-encode if image).
     * Returns the storage path after normalization.
     */
    public function storeNormalized(UploadedFile $file, string $disk, string $directory): string
    {
        $path = $file->store($directory, $disk);
        $mimeType = $file->getMimeType();

        // Normalize raster images (re-encode to remove embedded scripts/EXIF).
        $normalizableFormats = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        if (in_array($mimeType, $normalizableFormats, true)) {
            try {
                $manager = new ImageManager(new Driver);
                $image = $manager->read(Storage::disk($disk)->get($path));

                // Re-encode to the original format.
                $encoded = match ($mimeType) {
                    'image/jpeg' => $image->toJpeg(),
                    'image/png' => $image->toPng(),
                    'image/webp' => $image->toWebp(),
                    'image/gif' => $image->toGif(),
                };

                Storage::disk($disk)->put($path, (string) $encoded);
            } catch (\Throwable) {
                // If normalization fails, keep the original file.
                // Validation rules already guarantee the file is a supported image.
            }
        }

        return $path;
    }

    /**
     * Store an uploaded file and create an Attachment record.
     */
    public function storeFile(UploadedFile $file, string $disk, string $directory): Attachment
    {
        $path = $this->storeNormalized($file, $disk, $directory);

        // Recalculate file size and mime type after normalization.
        $finalSize = Storage::disk($disk)->size($path);
        $finalMimeType = Storage::disk($disk)->mimeType($path);

        return Attachment::create([
            'type' => AttachmentType::File,
            'disk' => $disk,
            'path' => $path,
            'filename' => $file->getClientOriginalName(),
            'file_size' => $finalSize,
            'mime_type' => $finalMimeType,
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
