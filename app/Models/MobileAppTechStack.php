<?php

namespace App\Models;

use App\Traits\HasUuidV6;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MobileAppTechStack extends Model
{
    use HasUuidV6;

    protected $fillable = [
        'mobile_application_id',
        'tech_stack_id',
        'version',
    ];

    public function mobileApplication(): BelongsTo
    {
        return $this->belongsTo(MobileApplication::class);
    }

    public function techStack(): BelongsTo
    {
        return $this->belongsTo(TechStack::class);
    }
}
