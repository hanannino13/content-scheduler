<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Platform extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'type']; 

     public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'post_platform')
                    ->using(PostPlatform::class) 
                    ->withPivot('platform_status', 'platform_response')
                    ->withTimestamps();
    }
}
