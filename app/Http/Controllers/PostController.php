<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Http\Requests\PostRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PostController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $posts = Post::with('user')->orderBy('created_at', 'desc')->get();
        return response()->json($posts, 200);
    }

    public function store(PostRequest $request): JsonResponse
    {
        $post = Post::create([
            'title' => $request->title,
            'body' => $request->body,
            'user_id' => $request->user_id,
        ]);

        return response()->json($post, 201);
    }

    public function show($id): JsonResponse
    {
        $post = Post::with('user')->findOrFail($id);
        return response()->json($post, 200);
    }

    public function update($id, PostRequest $request): JsonResponse
    {
        $post = Post::where('id', $id)
            ->update([
                'title' => $request->title,
                'body' => $request->body,
                'user_id' => $request->user_id,
            ]);

        return response()->json($post, 200);
    }

    public function destroy($id): JsonResponse
    {
        Post::where('id', $id)
            ->delete();

        return response()->json([], 204);
    }
}
