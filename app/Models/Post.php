<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity; 
use Illuminate\Database\Eloquent\Relations\BelongsTo; 
use Illuminate\Database\Eloquent\Factories\HasFactory; 
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'content',
        'image_url',
        'scheduled_time',
        'status',
    ];

   protected $casts = [
        'scheduled_time' => 'datetime',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }


    public function user(): BelongsTo 
    {
        return $this->belongsTo(User::class);
    }

    public function platforms(): BelongsToMany 
    {
        return $this->belongsToMany(Platform::class, 'post_platforms')
                     ->using(PostPlatform::class) 
                     ->withPivot('platform_status', 'platform_response')
                     ->withTimestamps();
    }
}
