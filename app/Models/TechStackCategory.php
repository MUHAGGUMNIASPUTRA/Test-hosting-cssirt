<?php

namespace App\Models;

use App\Traits\HasUuidV6;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TechStackCategory extends Model
{
    use HasUuidV6;

    protected $fillable = [
        'name',
        'description',
    ];

    public function techStacks(): HasMany
    {
        return $this->hasMany(TechStack::class, 'category_id');
    }
}
