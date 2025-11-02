<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Post;
use App\Models\Like;

class PostController extends Controller
{
    /** 投稿一覧 */
    public function index()
    {
        $posts = Post::with(['user', 'comments.user'])
            ->latest()
            ->get();

        return response()->json($posts);
    }

    /** 投稿詳細 */
    public function show($id)
    {
        $post = Post::with(['user', 'comments.user'])->findOrFail($id);
        return response()->json($post);
    }

    /** 投稿作成 */
    /** 投稿作成 */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'text' => 'required|string|max:255',
        ]);

        if (!Auth::check()) {
            return response()->json(['message' => 'ログインが必要です'], 401);
        }

        $userId = Auth::id();
        $text = trim($validated['text']);

        // ✅ 直近1分以内に同一テキストが投稿されていないか確認
        $duplicate = Post::where('user_id', $userId)
            ->where('text', $text)
            ->where('created_at', '>=', now()->subMinute())
            ->exists();

        if ($duplicate) {
            return response()->json([
                'message' => '同じ内容の投稿が短時間に行われました。',
            ], 409); // 409 Conflict
        }

        $post = Post::create([
            'user_id' => $userId,
            'text' => $text,
            'likes' => 0,
        ]);

        return response()->json($post, 201);
    }


    /** 投稿削除 */
    public function destroy($id)
    {
        $post = Post::findOrFail($id);

        // ✅ 自分の投稿以外は削除不可
        if ($post->user_id !== Auth::id()) {
            return response()->json(['message' => '権限がありません'], 403);
        }

        $post->delete();
        return response()->json(['message' => '削除しました']);
    }

    /** いいねトグル */
    public function toggleLike($id)
    {
        $userId = Auth::id();
        $post = Post::findOrFail($id);

        $existingLike = DB::table('likes')
            ->where('user_id', $userId)
            ->where('post_id', $post->id)
            ->first();

        if ($existingLike) {
            DB::table('likes')
                ->where('user_id', $userId)
                ->where('post_id', $post->id)
                ->delete();

            $likeCount = DB::table('likes')
                ->where('post_id', $post->id)
                ->count();

            $post->update(['likes' => $likeCount]);

            \Log::info('✅ Like解除成功', ['user_id' => $userId, 'post_id' => $post->id]);

            return response()->json([
                'status' => 'unliked',
                'likes' => $likeCount,
            ]);
        }

        DB::table('likes')->insert([
            'user_id' => $userId,
            'post_id' => $post->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $likeCount = DB::table('likes')
            ->where('post_id', $post->id)
            ->count();

        $post->update(['likes' => $likeCount]);

        \Log::info('❤️ Like追加成功', ['user_id' => $userId, 'post_id' => $post->id]);

        return response()->json([
            'status' => 'liked',
            'likes' => $likeCount,
        ]);
    }
}
