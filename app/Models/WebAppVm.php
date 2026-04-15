<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WebAppVm extends Model
{
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
