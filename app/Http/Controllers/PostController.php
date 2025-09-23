<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Services\ContentModeration;

class PostController extends Controller
{
    public function index()
    {
        try {
            // Eager load user agar tidak n+1 problem
            $posts = Post::with(['user', 'likes', 'replies'])->orderBy('created_at', 'desc')->get();

            // dd($posts);
            // Kirim data ke view dashboard
            return view('dashboard', compact('posts'));

        } catch (\Throwable $e) {
            // Bisa redirect dengan error message
            return redirect()->back()->with('error', 'Failed to fetch posts: '.$e->getMessage());
        }
    }

    // Ambil satu post berdasarkan id
    public function show($id)
    {
        try {
            $post = Post::with('user')->findOrFail($id);

            return response()->json([
                'success' => true,
                'post'    => $post,
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Post not found',
            ], 404);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch post',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'content' => 'required|string|max:500',
            'institution' => 'nullable|string|max:255',
        ]);

        // Moderation check for hate speech (fail-open on errors)
        try {
            $moderation = app(ContentModeration::class)->checkForHateSpeech($validated['content']);
            if ($moderation['isHateSpeech'] ?? false) {
                return response()->json([
                    'success' => false,
                    'message' => 'Content contains hate speech and cannot be published.',
                    'reasons' => $moderation['reasons'] ?? [],
                ], 422);
            }
        } catch (\Throwable $e) {
            // Ignore moderation errors to not block posting
        }

        try {
            $post = DB::transaction(function () use ($validated) {
                // Insert post
                $post = Post::create([
                    'user_id'     => Auth::id(),
                    'content'     => $validated['content'],
                    'institution' => $validated['institution'] ?? null,
                ]);
    
                // Tambah point user
                User::whereKey(Auth::id())->increment('point', 10);
    
                return $post;
            });
    
            return response()->json([
                'success' =>  true,
                'message' => 'Post created successfully!',
                'post'    => $post->load(['user', 'likes', 'replies']), // eager load relasi user
            ], 201);
    
        } catch (\Throwable $e) {
            // kalau ada error rollback otomatis
            return response()->json([
                'message' => 'Failed to create post',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
