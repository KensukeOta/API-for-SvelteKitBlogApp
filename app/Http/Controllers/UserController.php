<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'password_confirmation' => Hash::make($request->password_confirmation),
        ]);

        return response()->json(['user' => $user], 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            return response()->json(['auth_user' => Auth::user()], 200);
        }

        throw ValidationException::withMessages([
            'email' => ['ログインに失敗しました'],
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return response()->json(['message' => 'ログアウトしました'], 200);
    }

    /**
     * ユーザー名からユーザーの情報を取得する
     *
     * @param string $name
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($name): JsonResponse
    {
        $user = User::where('name', $name)
            ->with([
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
            ->first();

        if (!$user) {
            return response()->json(['message' => 'ユーザーが見つかりません。'], 404);
        }

        return response()->json(['user' => $user], 200);
    }

    /**
     * ユーザーをフォローする
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function follow(Request $request): JsonResponse
    {
        $follower = User::findOrFail($request->follower_id);
        $followee = User::findOrFail($request->followee_id);

        $follower->followings()->attach($followee->id);

        return response()->json(['message' => 'フォローしました。'], 201);
    }

    /**
     * ユーザーのフォローを解除する
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function unfollow(Request $request): JsonResponse
    {
        $follower = User::findOrFail($request->follower_id);
        $followee = User::findOrFail($request->followee_id);

        $follower->followings()->detach($followee->id);

        return response()->json(['message' => 'アンフォローしました。'], 200);
    }

    /**
     * 特定のユーザーがフォローしているユーザーの一覧を取得する
     *
     * @param string $name
     * @return \Illuminate\Http\JsonResponse
     */
    public function followings($name)
    {
        $user = User::where('name', $name)->first();
        $followings = $user->followings()->orderBy('pivot_created_at', 'desc')->get();

        return response()->json(['followings' => $followings], 200);
    }

    /**
     * 特定のユーザーをフォローしているユーザーの一覧を取得する
     *
     * @param string $name
     * @return \Illuminate\Http\JsonResponse
     */
    public function followers($name)
    {
        $user = User::where('name', $name)->first();
        $followers = $user->followers()->orderBy('pivot_created_at', 'desc')->get();

        return response()->json(['followers' => $followers], 200);
    }
}
