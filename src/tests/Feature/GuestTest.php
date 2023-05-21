<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuestTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_request_home()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    public function test_guest_can_ping()
    {
        $response = $this->get('api/v1/ping');
        $response->assertStatus(200);
    }

    public function test_guest_can_fetch_subscription_plans()
    {
        $response = $this->get('api/v1/plans');
        $response->assertJson([
            'errors' => null,
            'hasError' => false,
            'message' => '',
            'object' => []
        ]);
    }

    public function test_guest_can_login_with_correct_credentials()
    {
        $user = User::factory()->create()->first();
        $response = $this->postJson('api/login', [
            'email' => $user->email,
            'password' => 'password'
        ]);

        $response->assertJsonStructure([
            'errors',
            'hasError',
            'message',
            'object',
            'token',
            'expired_at'
        ]);
    }

    public function test_guest_cannot_login_with_false_credentials()
    {
        $user = User::factory()->create()->first();
        $response = $this->postJson('api/login', [
            'email' => $user->email,
            'password' => 'wrongpassword'
        ]);

        $response->assertJson([
            'errors' => [
                'email' => [
                    'The provided credentials are incorrect.'
                ]
            ],
            'hasError' => true,
            'message' => 'The given data is invalid',
            'object' => null
        ]);
    }

    public function test_guest_deleted_user_cannot_login()
    {
        $user = User::factory()->create()->first();
        $usedEmail = (string) $user->email;
        $user->anonymize();
        $response = $this->postJson('api/login', [
            'email' => $usedEmail,
            'password' => 'password'
        ]);

        $response->assertJson([
            'errors' => [
                'email' => [
                    'The provided credentials are incorrect.'
                ]
            ],
            'hasError' => true,
            'message' => 'The given data is invalid',
            'object' => null
        ]);
    }

    public function test_guest_cannot_use_login_without_post()
    {
        $response = $this->get('api/login');
        $response->assertStatus(405);
        $response = $this->delete('api/login');
        $response->assertStatus(405);
        $response = $this->patch('api/login');
        $response->assertStatus(405);
        $response = $this->put('api/login');
        $response->assertStatus(405);
    }

    public function test_guest_cannot_logout()
    {
        $response = $this->get('api/logout');
        $response->assertStatus(405);
    }
}
