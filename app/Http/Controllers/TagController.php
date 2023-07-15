<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TagController extends Controller
{
    public function show($tagName): JsonResponse
    {
        $tag = Tag::where('name', $tagName)->firstOrFail();

        $posts = $tag->posts()->with(['user', 'likes', 'tags'])->orderBy('created_at', 'desc')->get();

        return response()->json(['posts' => $posts]);
    }
}
