<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_login_and_logout_with_phone(): void
    {
        $registerResponse = $this->postJson('/api/auth/register', [
            'full_name' => 'Ali Test',
            'phone' => '700000001',
            'password' => 'secret123',
            'vehicle_type' => 'car',
            'user_role' => 'customer',
        ]);

        $registerResponse->assertCreated()
            ->assertJsonPath('phone', '700000001');

        $this->assertDatabaseHas('users', [
            'phone' => '700000001',
            'full_name' => 'Ali Test',
        ]);

        $loginResponse = $this->postJson('/api/auth/login', [
            'phone' => '700000001',
            'password' => 'secret123',
        ]);

        $loginResponse->assertOk()
            ->assertJsonStructure(['access_token', 'token_type', 'user']);

        $token = $loginResponse->json('access_token');

        $this->withToken($token)
            ->getJson('/api/auth/me')
            ->assertOk()
            ->assertJsonPath('phone', '700000001');

        $this->withToken($token)
            ->postJson('/api/auth/logout')
            ->assertOk()
            ->assertJsonPath('message', 'تم تسجيل الخروج بنجاح');
    }
}
