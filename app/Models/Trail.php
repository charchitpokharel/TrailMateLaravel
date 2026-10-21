<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Trail extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'location', 'difficulty', 'distance_km', 'description', 'user_id'];

    protected $casts = ['distance_km' => 'decimal:2'];

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
