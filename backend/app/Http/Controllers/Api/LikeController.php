<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Like;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    // いいね追加
    public function store($postId)
    {
        $post = Post::findOrFail($postId);

        $exists = Like::where('user_id', Auth::id())
            ->where('post_id', $postId)
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'すでにいいね済みです。'], 409);
        }

        Like::create([
            'user_id' => Auth::id(),
            'post_id' => $postId,
        ]);

        return response()->json(['message' => 'いいねしました。']);
    }

    // いいね削除
    public function destroy($postId)
    {
        $like = Like::where('user_id', Auth::id())
            ->where('post_id', $postId)
            ->first();

        if (!$like) {
            return response()->json(['message' => 'いいねが見つかりません。'], 404);
        }

        $like->delete();

        return response()->json(['message' => 'いいねを取り消しました。']);
    }
}
