<?php

// File: app/Models/Document.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

/**
 * @property string $title
 * @property string $slug
 * @property string|null $description
 * @property string|null $draft_file_path
 * @property string|null $official_file_path
 * @property string|null $version
 * @property bool $is_public
 * @property int|null $document_area_id
 * @property string|null $file_size Virtual — set in controller/collection transform
 * @property bool|null $file_exists Virtual — set in controller/collection transform
 * @property string|null $status Virtual — set in controller/collection transform
 */
class Document extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'slug',
        'description',
        'draft_file_path',
        'official_file_path',
        'version',
        'published_at',
        'is_public',
        'document_area_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'published_at' => 'datetime',
        'is_public' => 'boolean',
    ];

    public function documentArea(): BelongsTo
    {
        return $this->belongsTo(DocumentArea::class);
    }

    /**
     * Get only published documents
     */
    public function scopePublished($query)
    {
        return $query->whereNotNull('published_at');
    }

    /**
     * Get the file size in human readable format
     */
    public function fileSize(): string
    {
        if ($this->draft_file_path && Storage::disk('public')->exists($this->draft_file_path)) {
            $bytes = Storage::disk('public')->size($this->draft_file_path);

            return $this->formatBytes($bytes);
        }

        return 'N/A';
    }

    /**
     * Get the download URL
     */
    public function downloadUrl(): string
    {
        return Storage::disk('public')->path($this->draft_file_path ?? '');
    }

    /**
     * Check if file exists
     */
    public function fileExists(): bool
    {
        return $this->draft_file_path !== null && Storage::disk('public')->exists($this->draft_file_path);
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = 0;
        while ($bytes >= 1024 && $i < \count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }

        return round($bytes, $precision).' '.$units[$i];
    }
}
