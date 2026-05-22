<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Camera extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'venue_id', 'stream_url', 'thumbnail_url', 'status'];

    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }
}
