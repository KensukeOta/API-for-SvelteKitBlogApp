<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\TagController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    $authUser = User::with([
            'posts' => function ($query) {
                $query->orderBy('created_at', 'desc');
            },
            'posts.user',
            'posts.likes',
            'likes' => function ($query) {
                $query->orderBy('created_at', 'desc');
            },
            'likes.post.user',
            'likes.post.likes' => function ($query) {
                $query->orderBy('created_at', 'desc');
            },
            'followings' => function ($query) {
                $query->orderBy('follows.created_at', 'desc');
            },
            'followers' => function ($query) {
                $query->orderBy('follows.created_at', 'desc');
            }
        ])
        ->find(Auth::id());

    return response()->json(['authUser' => $authUser], 200);
});

Route::controller(UserController::class)->group(function () {
    Route::post('/users', 'register')->name('users.register');
    Route::get('/users/{name}', 'show')->name('users.show');
    Route::get('/users/{name}/timeline', 'timeline')->name('users.timeline');
    Route::get('/users/{name}/followings', 'followings')->name('users.followings');
    Route::get('/users/{name}/followers', 'followers')->name('users.followings');
    Route::post('/users/follow', 'follow')->name('users.follow');
    Route::delete('/users/follow', 'unfollow')->name('users.unfollow');
});

Route::controller(PostController::class)->group(function () {
    Route::get('/posts', 'index')->name('posts.index');
    Route::post('/posts', 'store')->name('posts.store');
    Route::get('/posts/{id}', 'show')->whereNumber('id')->name('posts.show');
    Route::patch('/posts/{id}', 'update')->whereNumber('id')->name('posts.update');
    Route::delete('/posts/{id}', 'destroy')->whereNumber('id')->name('posts.destroy');

    Route::get('/posts/search', 'search')->name('posts.search');
});

Route::controller(LikeController::class)->group(function () {
    Route::post('/likes', 'like')->name('likes.like');
    Route::delete('/likes', 'unlike')->name('likes.unlike');
    Route::get('/likes/{id}', 'show')->whereNumber('id')->name('likes.show');
});

Route::controller(CommentController::class)->group(function () {
    Route::post('/comments', 'store')->name('comments.store');
    Route::get('/comments/{id}', 'show')->whereNumber('id')->name('comments.show');
    Route::patch('/comments/{id}', 'update')->whereNumber('id')->name('comments.update');
    Route::delete('/comments/{id}', 'destroy')->whereNumber('id')->name('comments.destroy');
});

Route::controller(TagController::class)->group(function () {
    Route::get('/tags/{tagName}', 'show')->name('tags.show');
});
