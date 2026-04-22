<?php

namespace App\Models;

use App\Traits\HasUuidV6;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentArea extends Model
{
    use HasFactory, HasUuidV6;

    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    public function documents()
    {
        return $this->hasMany(Document::class);
    }
}
