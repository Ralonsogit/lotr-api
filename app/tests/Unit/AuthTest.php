<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\JsonResponse;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_cannot_register_with_unexpected_fields()
    {
        $data = [
            'name' => 'Test User',
            'email' => 'test978@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'unexpected_field' => 'unexpected_value',
        ];

        // Add header Accept: application/json
        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->postJson('/api/v1/register', $data);

        // 400 expected
        $response->assertStatus(JsonResponse::HTTP_BAD_REQUEST);
    }

    public function test_user_can_register()
    {
        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->postJson('/api/v1/register', [
            'name' => 'Test User',
            'email' => 'test789@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('users', ['email' => 'test789@example.com']);
    }

    public function test_user_can_login()
    {
        $user = User::factory()->create([
            'password' => Hash::make('password123')
        ]);

        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->postJson('/api/v1/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['access_token', 'token_type']);
    }

    public function test_user_can_logout()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->withHeaders([
            'Accept' => 'application/json',
        ])->postJson('/api/v1/logout');

        $response->assertStatus(204);
        $this->assertTrue($user->tokens->isEmpty());
    }
}
