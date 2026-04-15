<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MobileAppTechStack extends Model
{
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
