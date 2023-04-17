<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;

class PostController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'title' => ['required', 'between:1,50'],
            'body' => ['required', 'max:1000'],
            'user_id' => ['required'],
        ]);

        Post::create([
            'title' => $request->title,
            'body' => $request->body,
            'user_id' => $request->user_id,
        ]);
    }
}
