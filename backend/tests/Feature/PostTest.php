<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Post;
use App\Models\Like;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class PostTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function 投稿一覧を取得できる(): void
    {
        $user = User::factory()->create();
        Post::factory()->count(3)->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->getJson('/api/posts');

        $response->assertStatus(200)->assertJsonCount(3);
    }

    #[Test]
    public function 投稿を作成できる(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/posts', [
            'text' => 'テスト投稿',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('posts', ['text' => 'テスト投稿']);
    }

    #[Test]
    public function 投稿内容が空では登録できない(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/posts', [
            'text' => '',
        ]);

        $response->assertStatus(422);
    }

    #[Test]
    public function 投稿を削除できる(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->deleteJson("/api/posts/{$post->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
    }

    #[Test]
    public function 他人の投稿は削除できない(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $userB->id]);

        $response = $this->actingAs($userA)->deleteJson("/api/posts/{$post->id}");

        $response->assertStatus(403);
        $this->assertDatabaseHas('posts', ['id' => $post->id]);
    }

    #[Test]
    public function いいねを追加できる(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->putJson("/api/posts/{$post->id}/like");

        $response->assertStatus(200);
        $this->assertDatabaseHas('likes', [
            'user_id' => $user->id,
            'post_id' => $post->id
        ]);
    }

    #[Test]
    public function いいねを解除できる(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);

        // 1回目：いいね
        $this->actingAs($user)->putJson("/api/posts/{$post->id}/like");

        // 2回目：解除
        $response = $this->actingAs($user)->putJson("/api/posts/{$post->id}/like");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('likes', [
            'user_id' => $user->id,
            'post_id' => $post->id
        ]);
    }
}
