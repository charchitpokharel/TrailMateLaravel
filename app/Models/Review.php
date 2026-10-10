<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    use HasFactory;

    protected $fillable = ['trail_id', 'reviewer_name', 'rating', 'comment'];

    protected $casts = ['rating' => 'integer'];

    public function trail(): BelongsTo
    {
        return $this->belongsTo(Trail::class);
    }
}
