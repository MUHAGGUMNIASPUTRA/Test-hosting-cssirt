<?php

// Tujuan: Model untuk menyimpan daftar subdomain
// Caller: SubdomainController
// Side Effects: none

namespace App\Models;

use App\Traits\HasUuidV6;
use Illuminate\Database\Eloquent\Model;

class Subdomain extends Model
{
    use HasUuidV6;

    protected $fillable = [
        'subdomain',
        'description',
    ];
}
