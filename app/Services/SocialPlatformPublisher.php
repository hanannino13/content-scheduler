<?php

namespace App\Services;

use App\Models\Post;    
use App\Models\Platform; 
use Illuminate\Support\Facades\Log; 

class SocialPlatformPublisher
{
    /**
     * Mocks the process of publishing a post to a specific social media platform.
     *
     * @param Post $post The post to be published.
     * @param Platform $platform The platform to publish to.
     * @return array An array containing 'status' ('published' or 'failed') and 'message'.
     */
    public function mockPublishToPlatform(Post $post, Platform $platform): array
    {
        Log::info("Attempting mock publish for Post ID: {$post->id} on Platform: {$platform->name} ({$platform->type})");

        $delay = rand(1, 4);
        sleep($delay);
        Log::debug("Mock publish for Post ID: {$post->id} on {$platform->name} delayed for {$delay} seconds.");

        $content = $post->content;
        $imageUrl = $post->image_url;

        switch ($platform->type) {
            case 'twitter':
                if (mb_strlen($content) > 280) {
                    Log::warning("Post ID: {$post->id} failed Twitter validation (char limit).");
                    return ['status' => 'failed', 'message' => 'Twitter: Content exceeds 280 characters.'];
                }
                break;

            case 'instagram':
                if (empty($imageUrl)) {
                    Log::warning("Post ID: {$post->id} failed Instagram validation (no image).");
                    return ['status' => 'failed', 'message' => 'Instagram: An image URL is required for posts.'];
                }
                if (mb_strlen($content) > 2200) {
                    Log::warning("Post ID: {$post->id} failed Instagram validation (char limit).");
                    return ['status' => 'failed', 'message' => 'Instagram: Content exceeds 2200 characters.'];
                }
                break;

            case 'linkedin':
                if (mb_strlen($content) > 3000) {
                    Log::warning("Post ID: {$post->id} failed LinkedIn validation (char limit).");
                    return ['status' => 'failed', 'message' => 'LinkedIn: Content exceeds 3000 characters.'];
                }
                break;

            case 'facebook':
                break;

            default:
                break;
        }


        $successRate = 85;
        if (rand(1, 100) <= $successRate) {
            Log::info("Post ID: {$post->id} successfully mocked published to {$platform->name}.");
            return ['status' => 'published', 'message' => "Successfully published to {$platform->name}."];
        } else {
            Log::error("Post ID: {$post->id} failed to mock publish to {$platform->name} (simulated error).");
            return ['status' => 'failed', 'message' => "Mock API error: Failed to reach {$platform->name}."];
        }
    }
}