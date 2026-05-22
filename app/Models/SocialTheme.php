<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SocialTheme extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'mention_count'];

    public function mentions(): HasMany
    {
        return $this->hasMany(SocialMention::class, 'theme_id');
    }
}
