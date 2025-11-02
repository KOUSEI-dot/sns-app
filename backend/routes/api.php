<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\CommentController;

// ------------------------------------------------------------
// 🔐 認証系API（Sanctumトークンベース）
// ------------------------------------------------------------
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

// ------------------------------------------------------------
// 🌐 公開ルート（誰でも閲覧可能）
// ------------------------------------------------------------
Route::get('/posts', [PostController::class, 'index']);
Route::get('/posts/{id}', [PostController::class, 'show']);

// ------------------------------------------------------------
// 🔒 認証必須ルート（auth:sanctum）
// ------------------------------------------------------------
Route::middleware('auth:sanctum')->group(function () {
    // 投稿
    Route::post('/posts', [PostController::class, 'store']);
    Route::delete('/posts/{id}', [PostController::class, 'destroy']);
    Route::put('/posts/{id}/like', [PostController::class, 'toggleLike']);

    // コメント
    Route::post('/posts/{postId}/comments', [CommentController::class, 'store']);
    Route::delete('/comments/{id}', [CommentController::class, 'destroy']);
});
