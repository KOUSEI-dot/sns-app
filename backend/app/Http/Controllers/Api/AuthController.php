<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /** 🔹 ユーザー登録 */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $token = $user->createToken('api_token')->plainTextToken;

        return response()->json([
            'message' => '登録成功',
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    /** 🔹 ログイン */
    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['認証に失敗しました。'],
            ]);
        }

        // ✅ ログイン成功 → 新トークン発行
        $token = $user->createToken('api_token')->plainTextToken;

        return response()->json([
            'message' => 'ログイン成功',
            'user' => $user,
            'token' => $token,
        ]);
    }

    /** 🔹 ログアウト */
    public function logout(Request $request)
    {
        $user = $request->user();

        // 🔸 currentAccessToken() が存在していて、deleteメソッドを持つ場合のみ削除
        if ($user && $user->currentAccessToken() && method_exists($user->currentAccessToken(), 'delete')) {
            $user->currentAccessToken()->delete();
        }

        return response()->json(['message' => 'ログアウトしました。'], 200);
    }
}
