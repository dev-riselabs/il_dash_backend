<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SocialMention extends Model
{
    use HasFactory;

    protected $fillable = [
        'platform', 'author_handle', 'author_name', 'author_avatar', 'author_verified',
        'post_id', 'content', 'posted_at', 'likes', 'retweets', 'comments',
        'impressions', 'reach', 'sentiment_label', 'sentiment_score',
        'theme_id', 'hashtags', 'location',
    ];

    protected $casts = [
        'posted_at' => 'datetime',
        'hashtags' => 'array',
        'author_verified' => 'boolean',
        'sentiment_score' => 'float',
    ];

    public function theme(): BelongsTo
    {
        return $this->belongsTo(SocialTheme::class, 'theme_id');
    }
}
