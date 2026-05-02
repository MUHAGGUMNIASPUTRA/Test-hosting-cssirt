<?php

namespace App\Models;

use App\Traits\HasUuidV6;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WebAppVm extends Model
{
    use HasUuidV6;

    protected $fillable = [
        'web_application_id',
        'processor',
        'ram',
        'hdd',
        'sort_order',
    ];

    public function webApplication(): BelongsTo
    {
        return $this->belongsTo(WebApplication::class);
    }
}
