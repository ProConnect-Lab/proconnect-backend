<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_a_pro_account(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Jean Dupont',
            'email' => 'jean@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'account_type' => 'pro',
            'address' => '12 rue des Lilas, Paris',
            'company_name' => 'Dupont Consulting',
            'cfe_number' => 'CFE123456',
            'company_address' => '42 avenue Victor Hugo, Paris',
        ]);

        $response->assertCreated()
            ->assertJsonStructure(['token', 'user' => ['companies']]);

        $this->assertDatabaseHas('users', [
            'email' => 'jean@example.com',
            'account_type' => 'pro',
        ]);

        $this->assertDatabaseHas('companies', [
            'name' => 'Dupont Consulting',
            'cfe_number' => 'CFE123456',
        ]);
    }

    public function test_user_can_login_and_receive_a_token(): void
    {
        $this->postJson('/api/register', [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'account_type' => 'private',
            'address' => '10 Downing Street',
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'jane@example.com',
            'password' => 'password123',
        ]);

        $response->assertOk()
            ->assertJsonStructure(['token', 'user']);
    }
}
