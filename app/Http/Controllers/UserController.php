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
}
