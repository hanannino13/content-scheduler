<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot; 

class PostPlatform extends Pivot
{
    protected $table = 'post_platform';

    protected $fillable = [
        'post_id',
        'platform_id',
        'platform_status',
        'platform_response',
    ];

    protected $casts = [
    ];

    public $incrementing = false; 

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function platform()
    {
        return $this->belongsTo(Platform::class);
    }


    public function isPending(): bool
    {
        return $this->platform_status === 'pending';
    }

    public function isPublished(): bool
    {
        return $this->platform_status === 'published';
    }

    public function isFailed(): bool
    {
        return $this->platform_status === 'failed';
    }

    public function isIgnored(): bool
    {
        return $this->platform_status === 'ignored';
    }

    public function markAsPublished(string $response = null): void
    {
        $this->update([
            'platform_status' => 'published',
            'platform_response' => $response,
        ]);
    }

    public function markAsFailed(string $response = null): void
    {
        $this->update([
            'platform_status' => 'failed',
            'platform_response' => $response,
        ]);
    }
}