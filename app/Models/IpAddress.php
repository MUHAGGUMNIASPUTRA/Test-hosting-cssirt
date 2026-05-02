<?php

// Tujuan: Model untuk menyimpan alamat IP (private wajib, public opsional)
// Caller: IpAddressController
// Side Effects: none

namespace App\Models;

use App\Traits\HasUuidV6;
use Illuminate\Database\Eloquent\Model;

class IpAddress extends Model
{
    use HasUuidV6;

    protected $fillable = [
        'private_ip',
        'public_ip',
        'description',
    ];
}
