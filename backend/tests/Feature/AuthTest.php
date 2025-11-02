<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function 新規登録が成功する(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertStatus(201)
                 ->assertJsonStructure(['user' => ['id', 'name', 'email']]);

        $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
    }

    #[Test]
    public function 必須項目がない場合は登録できない(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => '',
            'email' => '',
            'password' => '',
        ]);

        $response->assertStatus(422);
    }

    #[Test]
    public function ログインが成功する(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure(['token']);
    }

    #[Test]
    public function ログイン失敗時はエラーが返る(): void
    {
        $response = $this->postJson('/api/login', [
            'email' => 'no_user@example.com',
            'password' => 'wrongpass',
        ]);

        // Laravel標準バリデーションエラーは422
        $response->assertStatus(422);
    }

    #[Test]
    public function ログアウトできる(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson('/api/logout');

        $response->assertStatus(200)
         ->assertJson(['message' => 'ログアウトしました。']);

    }
}
