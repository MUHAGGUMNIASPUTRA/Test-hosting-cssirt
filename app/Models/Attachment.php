<?php

namespace App\Models;

use App\Enums\AttachmentType;
use App\Traits\HasUuidV6;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

/**
 * @property AttachmentType $type
 * @property string|null $disk
 * @property string|null $path
 * @property string|null $url
 * @property string|null $filename
 * @property int|null $file_size
 * @property string|null $mime_type
 */
class Attachment extends Model
{
    use HasFactory, HasUuidV6;

    protected $fillable = [
        'type',
        'disk',
        'path',
        'url',
        'filename',
        'file_size',
        'mime_type',
    ];

    protected $casts = [
        'type' => AttachmentType::class,
        'file_size' => 'integer',
    ];

    /**
     * Whether the attachment is a file stored on disk.
     */
    public function isFile(): bool
    {
        return $this->type === AttachmentType::File;
    }

    /**
     * Whether the attachment is an external link.
     */
    public function isLink(): bool
    {
        return $this->type === AttachmentType::Link;
    }

    /**
     * The publicly accessible URL for this attachment.
     * - Links: the raw URL.
     * - Files on 'public' disk: /storage/{path}.
     * - Files on 'local' disk: null (served via signed route).
     */
    public function displayUrl(): ?string
    {
        if ($this->isLink()) {
            return $this->url;
        }

        if ($this->disk === 'public' && $this->path) {
            return '/storage/'.$this->path;
        }

        return null;
    }

    /**
     * Human-readable file size string (e.g. "1.23 MB").
     * Returns null for links or when size is unknown.
     */
    public function formattedFileSize(): ?string
    {
        if (! $this->file_size) {
            return null;
        }

        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = $this->file_size;
        $i = 0;

        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }

        return round($bytes, 2).' '.$units[$i];
    }

    /**
     * Delete the underlying stored file (if applicable) when the model is deleted.
     */
    protected static function booted(): void
    {
        static::deleting(function (Attachment $attachment) {
            if ($attachment->isFile() && $attachment->disk && $attachment->path) {
                Storage::disk($attachment->disk)->delete($attachment->path);
            }
        });
    }
}
