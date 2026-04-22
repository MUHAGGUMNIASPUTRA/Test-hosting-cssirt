<?php

namespace App\Models;

use App\Traits\HasUuidV6;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WebAppNetwork extends Model
{
    use HasUuidV6;

    protected $fillable = [
        'web_application_id',
        'environment',
        'dns',
        'local_ip',
        'public_ip',
        'is_primary',
        'sort_order',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    public function webApplication(): BelongsTo
    {
        return $this->belongsTo(WebApplication::class);
    }
}
