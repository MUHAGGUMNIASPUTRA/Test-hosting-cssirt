<?php

namespace App\Models;

use App\Traits\HasUuidV6;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property Subdomain|null $subdomain
 * @property IpAddress|null $ipAddress
 */
class WebAppNetwork extends Model
{
    use HasUuidV6;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'web_application_id',
        'environment',
        'dns',
        'local_ip',
        'public_ip',
        'ip_address_id',
        'subdomain_id',
        'is_primary',
        'sort_order',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'is_primary' => 'boolean',
    ];

    public function webApplication(): BelongsTo
    {
        return $this->belongsTo(WebApplication::class);
    }

    public function ipAddress(): BelongsTo
    {
        return $this->belongsTo(IpAddress::class);
    }

    public function subdomain(): BelongsTo
    {
        return $this->belongsTo(Subdomain::class);
    }
}
