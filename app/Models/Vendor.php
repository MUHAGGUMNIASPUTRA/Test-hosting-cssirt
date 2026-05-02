<?php

namespace App\Models;

use App\Traits\HasUuidV6;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    use HasUuidV6;

    protected $fillable = [
        'company_name',
        'location',
        'phone',
        'email',
        'notes',
        'pic_name',
        'pic_phone',
        'pic_email',
    ];
}
