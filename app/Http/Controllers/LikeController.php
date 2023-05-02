<?php

namespace App\Http\Controllers;

use App\Models\Like;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class LikeController extends Controller
{
    public function like(Request $request): JsonResponse
    {
        // 既にいいねされているかチェック
        $existingLike = Like::where('user_id', $request->user_id)
            ->where('post_id', $request->post_id)
            ->first();

        if ($existingLike) {
            $this->unlike($request);
            return response()->json(['message' => 'いいねを取り消しました'], 200);
        }

        $like = Like::create([
            'user_id' => $request->user_id,
            'post_id' => $request->post_id,
        ]);

        return response()->json(['like' => $like], 201);
    }

    public function unlike(Request $request): JsonResponse
    {
        Like::where('user_id', $request->user_id)
            ->where('post_id', $request->post_id)
            ->delete();

        return response()->json(['message' => 'いいねを取り消しました'], 200);
    }

    public function show($id): JsonResponse
    {
        $posts = Post::select('posts.*')
            ->join('likes', 'posts.id', '=', 'likes.post_id')
            ->where('likes.user_id', $id)
            ->with(['user', 'likes'])
            ->withCount('likes')
            ->orderBy('likes.created_at', 'desc')
            ->get();

        return response()->json(['posts' => $posts], 200);
    }
}
