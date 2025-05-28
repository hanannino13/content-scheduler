<?php

namespace App\Jobs;

use App\Models\Post;
use App\Services\SocialPlatformPublisher; 
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log; 

class PublishPostJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $post;

    /**
     * Create a new job instance.
     */
    public function __construct(Post $post)
    {
        $this->post = $post;
    }

    /**
     * Execute the job.
     */
    public function handle(SocialPlatformPublisher $publisher): void
    {
        Log::info("Attempting to publish Post ID: {$this->post->id}");

        if (!in_array($this->post->status, ['scheduled', 'publishing'])) {
            Log::warning("Post ID {$this->post->id} is not in 'scheduled' or 'publishing' state. Skipping.");
            return;
        }

        $this->post->update(['status' => 'publishing']); 

        $allPublishedSuccessfully = true;

        foreach ($this->post->platforms as $platform) {
            if (in_array($platform->pivot->platform_status, ['published', 'failed'])) {
                continue;
            }

            $result = $publisher->mockPublishToPlatform($this->post, $platform);

            $this->post->platforms()->updateExistingPivot($platform->id, [
                'platform_status' => $result['status'],
                'platform_response' => $result['message'],
                'updated_at' => now(), 
            ]);

            if ($result['status'] === 'failed') {
                $allPublishedSuccessfully = false;
            }
        }

        if ($allPublishedSuccessfully) {
            $this->post->update(['status' => 'published']);
            Log::info("Post ID {$this->post->id} published successfully on all selected platforms.");
        } else {
            $anyPlatformSucceeded = $this->post->platforms()->wherePivot('platform_status', 'published')->exists();
            $this->post->update(['status' => $anyPlatformSucceeded ? 'partially_published' : 'failed']);
            Log::warning("Post ID {$this->post->id} finished with mixed or failed results. Status: {$this->post->status}");
        }
    }
}