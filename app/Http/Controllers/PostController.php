<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PostController extends Controller
{
    /**
     * Display a listing of posts.
     * Uses Policy authorization: viewAny
     */
    public function index(Request $request): JsonResponse
    {
        // Check if user can view any posts
        $this->authorize('viewAny', Post::class);

        $posts = Post::with('author')->paginate(15);

        return response()->json([
            'message' => 'Posts retrieved successfully.',
            'data' => $posts,
        ]);
    }

    /**
     * Store a newly created post in storage.
     * Uses Policy authorization: create
     */
    public function store(Request $request): JsonResponse
    {
        // Check if user can create posts
        $this->authorize('create', Post::class);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'status' => 'sometimes|in:draft,published',
        ]);

        $validated['user_id'] = auth()->id();

        $post = Post::create($validated);

        return response()->json([
            'message' => 'Post created successfully.',
            'data' => $post->load('author'),
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified post.
     * Uses Policy authorization: view
     */
    public function show(Post $post): JsonResponse
    {
        // Check if user can view this post
        $this->authorize('view', $post);

        return response()->json([
            'message' => 'Post retrieved successfully.',
            'data' => $post->load('author'),
        ]);
    }

    /**
     * Update the specified post in storage.
     * Uses Policy authorization: update
     */
    public function update(Request $request, Post $post): JsonResponse
    {
        // Check if user can update this post
        $this->authorize('update', $post);

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'content' => 'sometimes|string',
            'status' => 'sometimes|in:draft,published',
        ]);

        $post->update($validated);

        return response()->json([
            'message' => 'Post updated successfully.',
            'data' => $post->refresh(),
        ]);
    }

    /**
     * Remove the specified post from storage.
     * Uses Policy authorization: delete
     */
    public function destroy(Post $post): JsonResponse
    {
        // Check if user can delete this post
        $this->authorize('delete', $post);

        $post->delete();

        return response()->json([
            'message' => 'Post deleted successfully.',
        ]);
    }

    /**
     * Publish a post.
     * Uses Policy authorization: publish
     */
    public function publish(Post $post): JsonResponse
    {
        // Check if user can publish this post
        $this->authorize('publish', $post);

        $post->update([
            'status' => 'published',
            'published_at' => now(),
        ]);

        return response()->json([
            'message' => 'Post published successfully.',
            'data' => $post,
        ]);
    }
}
