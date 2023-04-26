<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\LikeController;

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
    return $request->user();
});

Route::controller(UserController::class)->group(function () {
    Route::post('/users', 'register')->name('users.register');
});

Route::controller(PostController::class)->group(function () {
    Route::get('/posts', 'index')->name('posts.index');
    Route::post('/posts', 'store')->name('posts.store');
    Route::get('/posts/{id}', 'show')->whereNumber('id')->name('posts.show');
    Route::patch('/posts/{id}', 'update')->whereNumber('id')->name('posts.update');
    Route::delete('/posts/{id}', 'destroy')->whereNumber('id')->name('posts.destroy');
});

Route::controller(LikeController::class)->group(function () {
    Route::post('/likes', 'like')->name('likes.like');
    Route::delete('/likes', 'unlike')->name('likes.unlike');
});
