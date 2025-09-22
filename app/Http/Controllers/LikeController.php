<?php

namespace App\Http\Controllers;

use App\Models\Post;    
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    public function toggleLike(Post $post)
    {
        $user = auth()->user();

        if ($post->isLikedBy($user)) {
            // sudah like → hapus
            $post->likes()->where('user_id', $user->id)->delete();
            $liked = false;
        } else {
            // belum like → tambah
            $post->likes()->create([
                'user_id' => $user->id,
            ]);
            $liked = true;
        }

        return response()->json([
            'success' => true,
            'liked' => $liked,
            'likes_count' => $post->likes()->count(),
        ]);
    }
}
