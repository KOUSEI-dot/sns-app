<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Post;
use App\Models\Comment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function コメントを投稿できる(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();

        $response = $this->actingAs($user)->postJson("/api/posts/{$post->id}/comments", [
            'text' => 'ナイス投稿！', // ← リクエストは text のままでOK
        ]);

        $response->assertStatus(201);

        // ✅ DBには content カラムとして保存される
        $this->assertDatabaseHas('comments', [
            'post_id' => $post->id,
            'content' => 'ナイス投稿！',
        ]);
    }

    #[Test]
    public function コメントが空の場合は登録できない(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();

        $response = $this->actingAs($user)->postJson("/api/posts/{$post->id}/comments", [
            'text' => '',
        ]);

        $response->assertStatus(422);
    }

    #[Test]
    public function 投稿のコメント一覧を取得できる(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();

        Comment::factory()->count(2)->create([
            'post_id' => $post->id,
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->getJson("/api/posts/{$post->id}");

        $response->assertStatus(200)
                 ->assertJsonStructure(['comments']);
    }
}
