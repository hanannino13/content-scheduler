<?php

namespace App\Http\Controllers\Web;

use App\Models\Post;
use App\Models\Platform;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse; 
use Illuminate\Validation\ValidationException; 

class PostController extends Controller
{
    public function create()
    {
        $platforms = Platform::all();
        return view('posts.create', compact('platforms'));
    }

    public function edit(Post $post)
    {
        if ($post->user_id !== Auth::id()) {
            abort(403, 'Unauthorized. You do not own this post.');
        }

        $platforms = Platform::all();
        $selectedPlatformIds = $post->platforms->pluck('id')->toArray();

        return view('posts.edit', compact('post', 'platforms', 'selectedPlatformIds'));
    }

    public function dashboard(Request $request)
    {
        $user = Auth::user();
        $query = $user->posts()->with('platforms')->latest('scheduled_time');

        // Initial load with potential filters from URL
        $statusFilter = $request->get('status');
        if ($statusFilter) {
            $query->where('status', $statusFilter);
        }

        $platformFilterId = $request->get('platform_id');
        if ($platformFilterId) {
            $query->whereHas('platforms', function ($q) use ($platformFilterId) {
                $q->where('platforms.id', $platformFilterId);
            });
        }

        $posts = $query->paginate(10); 

        $platforms = Platform::all(); 

        return view('dashboard', compact('posts', 'platforms', 'statusFilter'));
    }

    public function store(Request $request): RedirectResponse
    {
        try {
            $validatedData = $request->validate([
                'title' => 'nullable|string|max:255',
                'content' => 'required|string',
                'scheduled_time' => 'required|date|after_or_equal:now',
                'image_url' => 'nullable|url',
                'platform_ids' => 'required|array|min:1',
                'platform_ids.*' => 'exists:platforms,id',
            ]);

            $post = new Post();
            $post->user_id = Auth::id();
            $post->title = $validatedData['title'] ?? null;
            $post->content = $validatedData['content'];
            $post->scheduled_time = $validatedData['scheduled_time'];
            $post->image_url = $validatedData['image_url'] ?? null;
            $post->status = 'scheduled';

            $post->save();

            $post->platforms()->sync($validatedData['platform_ids']);

            return redirect()->route('dashboard')->with('success', 'Post created successfully!');

        } catch (ValidationException $e) {
            Log::error('Validation error when storing post:', ['errors' => $e->errors()]);
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Error storing post:', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return back()->with('error', 'An error occurred while creating the post. Please try again.')->withInput();
        }
    }

}