<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Http\Requests\PostRequest;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $posts = Post::with('user')->orderBy('created_at', 'desc')->get();
        return response()->json($posts);
    }

    public function store(PostRequest $request)
    {
        Post::create([
            'title' => $request->title,
            'body' => $request->body,
            'user_id' => $request->user_id,
        ]);
    }

    public function show($id)
    {
        $post = Post::with('user')->findOrFail($id);
        return response()->json($post);
    }

    public function update($id, PostRequest $request)
    {
        Post::where('id', $id)
            ->update([
                'title' => $request->title,
                'body' => $request->body,
                'user_id' => $request->user_id,
            ]);
    }

    public function destroy($id)
    {
        Post::where('id', $id)
            ->delete();
    }
}
