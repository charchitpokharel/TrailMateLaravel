<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Trail extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'location', 'difficulty', 'distance_km', 'description'];

    protected $casts = ['distance_km' => 'decimal:2'];

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }
}
