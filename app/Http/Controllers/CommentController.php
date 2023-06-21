<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Http\Requests\CommentRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CommentController extends Controller
{
    public function store(CommentRequest $request): JsonResponse
    {
        $comment = Comment::create([
            'body' => $request->body,
            'user_id' => $request->user_id,
            'post_id' => $request->post_id,
        ]);

        return response()->json(['comment' => $comment], 201);
    }

    public function update($id, CommentRequest $request): JsonResponse
    {
        Comment::findOrFail($id)
            ->update([
                'body' => $request->body,
                'user_id' => $request->user_id,
                'post_id' => $request->post_id,
            ]);

        $comment = Comment::findOrFail($id);

        return response()->json(['comment' => $comment], 200);
    }

    public function destroy($id): JsonResponse
    {
        Comment::findOrFail($id)
            ->delete();

        return response()->json([], 204);
    }
}
