<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Tag;
use App\Http\Requests\PostRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PostController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $posts = Post::with(['user', 'likes', 'tags'])->orderBy('created_at', 'desc')->get();
        return response()->json(['posts' => $posts], 200);
    }

    public function store(PostRequest $request): JsonResponse
    {
        $post = Post::create([
            'title' => $request->title,
            'body' => $request->body,
            'user_id' => $request->user_id,
        ]);

        // タグの投稿処理
        $tags = $request->tags; // リクエストから受け取ったタグの配列
        $tagIds = [];

        foreach ($tags as $tagName) {
            if (!empty($tagName)) {
                $tag = Tag::firstOrCreate(['name' => $tagName]);
                $tagIds[] = $tag->id;
            }
        }

        $post->tags()->attach($tagIds);

        return response()->json(['post' => $post], 201)
            ->header('Location', route('posts.show', ['id' => $post->id]));
    }

    public function show($id): JsonResponse
    {
        $post = Post::with(['user', 'likes', 'comments.user', 'tags'])->findOrFail($id);
        return response()->json(['post' => $post], 200);
    }

    public function update($id, PostRequest $request): JsonResponse
    {
        $post = Post::findOrFail($id);

        $post->update([
            'title' => $request->title,
            'body' => $request->body,
            'user_id' => $request->user_id,
        ]);

        // タグの更新処理
        $tags = $request->tags; // リクエストから受け取ったタグの配列
        $tagIds = [];

        foreach ($tags as $tagName) {
            if (!empty($tagName)) {
                $tag = Tag::firstOrCreate(['name' => $tagName]);
                $tagIds[] = $tag->id;
            }
        }

        $post->tags()->sync($tagIds);

        return response()->json(['post' => $post], 200);
    }

    public function destroy($id): JsonResponse
    {
        Post::findOrFail($id)
            ->delete();

        return response()->json([], 204);
    }

    public function search(Request $request): JsonResponse
    {
        $searchQuery = $request->query('q');

        $posts = Post::with(['user', 'likes', 'tags'])
            ->whereHas('user', function ($query) use ($searchQuery) {
                $query->where('name', 'LIKE', "%{$searchQuery}%");
            })
            ->orWhere('title', 'LIKE', "%{$searchQuery}%")
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['posts' => $posts]);
    }
}
