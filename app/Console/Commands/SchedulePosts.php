<?php

namespace App\Console\Commands;

use App\Jobs\PublishPostJob;
use App\Models\Post;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SchedulePosts extends Command
{
    protected $signature = 'app:schedule-posts'; 
    protected $description = 'Dispatches jobs for posts that are due for publication.';

    public function handle()
    {
        Log::info('Checking for due posts...');

        $duePosts = Post::where('status', 'scheduled')
                        ->where('scheduled_time', '<=', now())
                        ->get();

        if ($duePosts->isEmpty()) {
            Log::info('No posts due for publication.');
            $this->info('No posts due for publication.');
            return Command::SUCCESS;
        }

        $this->info("Found {$duePosts->count()} post(s) due for publication.");
        Log::info("Found {$duePosts->count()} post(s) due for publication.");

        foreach ($duePosts as $post) {
            PublishPostJob::dispatch($post);
            $post->update(['status' => 'publishing']);
            $this->info("Dispatched job for Post ID: {$post->id}");
            Log::info("Dispatched job for Post ID: {$post->id}");
        }

        return Command::SUCCESS;
    }
}