<?php

namespace App\Models;

use App\Traits\HasUuidV6;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Organization extends Model
{
    use HasUuidV6;

    protected $fillable = [
        'name',
        'it_contact_name',
        'it_contact_phone',
        'it_contact_email',
    ];

    public function departments(): HasMany
    {
        return $this->hasMany(Department::class);
    }

    public function locations(): HasMany
    {
        return $this->hasMany(Location::class);
    }

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }
}
