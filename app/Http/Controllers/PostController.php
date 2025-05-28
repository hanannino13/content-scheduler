<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Platform;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Validation\ValidationException; 
use Illuminate\Routing\Controllers\HasMiddleware; 

class PostController extends Controller
{
    /**
     * Define middleware for this controller.
     * All methods in this controller will require Sanctum authentication.
     */
    public function __construct()
    {
        // Apply 'auth:sanctum' middleware to all methods in this controller
        $this->middleware('auth:sanctum');

        // Apply 'throttle:scheduled-posts' middleware only to the 'store' method
        $this->middleware('throttle:scheduled-posts')->only('store');
    }

    /**
     * Display a listing of the user's posts with filters.
     * GET /api/posts
     */
    #[Route('GET', '/posts')]
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = $user->posts()->with('platforms');

        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('scheduled_time', [
                $request->input('start_date') . ' 00:00:00', 
                $request->input('end_date') . ' 23:59:59'    
            ]);
        } elseif ($request->has('date')) { 
            $date = $request->input('date');
            $query->whereDate('scheduled_time', $date);
        }

        if ($request->has('platform_id')) {
            $query->whereHas('platforms', function ($q) use ($request) {
                $q->where('platform_id', $request->input('platform_id'));
            });
        }

        $posts = $query->latest('scheduled_time')->paginate(10); // Paginate results

        return response()->json($posts);
    }

    /**
     * Store a newly created post in storage.
     * POST /api/posts
     */
    #[Route('POST', '/posts')]
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'content' => 'required|string', 
            'image_url' => 'nullable|url|max:2048', 
            'scheduled_time' => 'required|date|after:now', 
            'platform_ids' => 'required|array|min:1',
            'platform_ids.*' => 'exists:platforms,id', 
        ]);

        $selectedPlatforms = Platform::whereIn('id', $request->input('platform_ids'))->get();
        $content = $request->input('content');
        $validationErrors = [];

        foreach ($selectedPlatforms as $platform) {
            switch ($platform->type) {
                case 'twitter':
                    if (mb_strlen($content) > 280) {
                        $validationErrors['content_twitter'] = "Content for Twitter exceeds 280 characters.";
                    }
                    break;
                case 'instagram':
                    if (mb_strlen($content) > 2200) { 
                        $validationErrors['content_instagram'] = "Content for Instagram exceeds 2200 characters.";
                    }
                    if (empty($request->input('image_url'))) {
                        $validationErrors['image_instagram'] = "Instagram posts require an image URL.";
                    }
                    break;
                case 'linkedin':
                    if (mb_strlen($content) > 3000) { 
                        $validationErrors['content_linkedin'] = "Content for LinkedIn exceeds 3000 characters.";
                    }
                    break;
            }
        }

        if (!empty($validationErrors)) {
            throw ValidationException::withMessages($validationErrors);
        }

        $post = Auth::user()->posts()->create([
            'title' => $request->input('title'),
            'content' => $content,
            'image_url' => $request->input('image_url'),
            'scheduled_time' => $request->input('scheduled_time'),
            'status' => 'scheduled', 
        ]);

        $post->platforms()->attach($request->input('platform_ids'));

        return response()->json($post->load('platforms'), 201);
    }

    /**
     * Display the specified post.
     * GET /api/posts/{post}
     */
    #[Route('GET', '/posts/{post}')]
    public function show(Post $post)
    {
        if ($post->user_id !== Auth::id()) {
            abort(403, 'Unauthorized. You do not own this post.');
        }

        return response()->json($post->load('platforms'));
    }

    /**
     * Update the specified post in storage.
     * PUT /api/posts/{post}
     */
    #[Route('PUT', '/posts/{post}')]
    public function update(Request $request, Post $post)
    {
        if ($post->user_id !== Auth::id()) {
            abort(403, 'Unauthorized. You do not own this post.');
        }

        if (in_array($post->status, ['publishing', 'published', 'failed'])) {
            return response()->json(['message' => 'Cannot update a post that has already been processed.'], 400);
        }

        $request->validate([
            'title' => 'nullable|string|max:255',
            'content' => 'required|string', 
            'image_url' => 'nullable|url|max:2048',
            'scheduled_time' => 'required|date|after:now', 
            'platform_ids' => 'required|array|min:1',
            'platform_ids.*' => 'exists:platforms,id',
        ]);

        $selectedPlatforms = Platform::whereIn('id', $request->input('platform_ids'))->get();
        $content = $request->input('content');
        $validationErrors = [];

        foreach ($selectedPlatforms as $platform) {
            switch ($platform->type) {
                case 'twitter':
                    if (mb_strlen($content) > 280) {
                        $validationErrors['content_twitter'] = "Content for Twitter exceeds 280 characters.";
                    }
                    break;
                case 'instagram':
                    if (mb_strlen($content) > 2200) {
                        $validationErrors['content_instagram'] = "Content for Instagram exceeds 2200 characters.";
                    }
                    if (empty($request->input('image_url'))) {
                        $validationErrors['image_instagram'] = "Instagram posts require an image URL.";
                    }
                    break;
                case 'linkedin':
                    if (mb_strlen($content) > 3000) {
                        $validationErrors['content_linkedin'] = "Content for LinkedIn exceeds 3000 characters.";
                    }
                    break;
            }
        }

        if (!empty($validationErrors)) {
            throw ValidationException::withMessages($validationErrors);
        }

        $post->update([
            'title' => $request->input('title'),
            'content' => $content,
            'image_url' => $request->input('image_url'),
            'scheduled_time' => $request->input('scheduled_time'),
            'status' => 'scheduled',
        ]);

        
        $post->platforms()->sync($request->input('platform_ids'));

        return response()->json($post->load('platforms'));
    }

    /**
     * Remove the specified post from storage.
     * DELETE /api/posts/{post}
     */
    #[Route('DELETE', '/posts/{post}')]
    public function destroy(Post $post)
    {
        if ($post->user_id !== Auth::id()) {
            abort(403, 'Unauthorized. You do not own this post.');
        }

        if ($post->status === 'publishing') {
            return response()->json(['message' => 'Cannot delete a post that is currently being published.'], 400);
        }

        $post->delete();

        return response()->json(null, 204);
    }

    public function dashboard(Request $request)
    {
        $user = Auth::user();
        $query = $user->posts()->with('platforms')->latest('scheduled_time');

        $statusFilter = $request->get('status');
        if ($statusFilter) {
            $query->where('status', $statusFilter);
        }

        $posts = $query->paginate(10); 

        $platforms = Platform::all(); 

        return view('dashboard', compact('posts', 'platforms', 'statusFilter'));
    }
}