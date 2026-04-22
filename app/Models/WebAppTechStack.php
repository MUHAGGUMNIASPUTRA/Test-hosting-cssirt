<?php

namespace App\Models;

use App\Traits\HasUuidV6;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WebAppTechStack extends Model
{
    use HasUuidV6;

    protected $fillable = [
        'web_application_id',
        'tech_stack_id',
        'version',
    ];

    public function webApplication(): BelongsTo
    {
        return $this->belongsTo(WebApplication::class);
    }

    public function techStack(): BelongsTo
    {
        return $this->belongsTo(TechStack::class);
    }
}
