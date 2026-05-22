<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SocialHashtag extends Model
{
    use HasFactory;

    protected $fillable = ['tag', 'mention_count'];
}
