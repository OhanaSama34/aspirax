<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Reply;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ReplyController extends Controller
{
    public function replies(Post $post) {
        $comments = $post->replies()->with('user')->get();
        return response()->json(['comments' => $comments]);
    }
    
     // Simpan komentar baru
     public function store(Request $request, Post $post)
     {
         $request->validate([
             'content' => 'required|string|max:1000',
         ]);
 
         $reply = Reply::create([
             'post_id' => $post->id,
             'user_id' => Auth::id(),
             'content' => $request->content,
         ]);
         $reply->load('user');
         return response()->json([
             'success' => true,
             'reply' => $reply->load('user'),
         ]);
     }
 
     // Ambil detail post + semua komentar
     public function show(Post $post)
     {
         $post->load(['user', 'likes', 'replies.user']);
 
         return response()->json([
             'success' => true,
             'post' => $post,
         ]);
     }
}
