<?php

// File: app/Models/Document.php

namespace App\Models;

use App\Enums\DocumentStage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $title
 * @property string $slug
 * @property string|null $description
 * @property string|null $draft_file_path URL-only link to Word draft (admin-only)
 * @property \App\Models\Attachment|null $officialAttachment
 * @property string|null $reference_number
 * @property DocumentStage|null $stage
 * @property string|null $version
 * @property bool $is_public
 * @property int|null $document_area_id
 * @property string|null $pub_status Virtual — set in service transform
 */
class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'draft_file_path',
        'official_attachment_id',
        'reference_number',
        'stage',
        'version',
        'published_at',
        'is_public',
        'document_area_id',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'is_public' => 'boolean',
        'stage' => DocumentStage::class,
    ];

    public function documentArea(): BelongsTo
    {
        return $this->belongsTo(DocumentArea::class);
    }

    /**
     * Get the official attachment (PDF upload or external link).
     */
    public function officialAttachment(): BelongsTo
    {
        return $this->belongsTo(Attachment::class, 'official_attachment_id');
    }

    public function scopePublished($query)
    {
        return $query->whereNotNull('published_at');
    }
}
