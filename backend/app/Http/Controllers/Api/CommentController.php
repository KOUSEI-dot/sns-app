<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Comment;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    /** コメント追加 */
    public function store(Request $request, $postId)
    {
        // 🧩 フロントが text を送っているなら text に合わせる
        $validated = $request->validate([
            'text' => 'required|string|max:255',
        ]);

        $comment = Comment::create([
            'post_id' => $postId,
            'user_id' => Auth::id(),
            'content' => $validated['text'], // DB列名が content ならここでマッピング
        ]);

        // ✅ コメントを返す（user情報付き）
        return response()->json($comment->load('user'), 201);
    }

    /** コメント削除 */
    public function destroy($id)
    {
        $comment = Comment::findOrFail($id);

        // ✅ 本人のみ削除可
        if ($comment->user_id !== Auth::id()) {
            return response()->json(['message' => '権限がありません'], 403);
        }

        $comment->delete();
        return response()->json(['message' => 'コメントを削除しました']);
    }
}
