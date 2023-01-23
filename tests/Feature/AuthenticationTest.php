<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_user_can_register_with_correct_credentials()
    {
        $response = $this->postJson('api/v1/auth/register', [
            'name' => 'andy',
            'email' => 'a@a.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['data'])
            ->assertJsonCount(3, 'data')
            ->assertJsonpath('data.name', 'andy');

        $this->assertDatabaseHas('users', ['name' => 'andy', 'email' => 'a@a.com']);
    }

    public function test_user_cannot_register_with_incorrect_credentials()
    {
        $response = $this->postJson('api/v1/auth/register', [
            'name' => 'andy',
            'email' => 'a@a.com',
            'password' => 'password',
            'password_confirmation' => 'passwordagain',
        ]);

        $response->assertStatus(422);

        $this->assertDatabaseMissing('users', ['name' => 'andy', 'email' => 'a@a.com']);
    }

    public function test_user_can_login_with_correct_credentials()
    {
        $user = User::factory()->create();
        
        $response = $this->postJson('api/v1/auth/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['access_token']);
    }

    public function test_user_cannot_login_with_incorrect_credentials()
    {
        $user = User::factory()->create();
        
        $response = $this->postJson('api/v1/auth/login', [
            'email' => $user->email,
            'password' => 'passwordagain',
        ]);

        $response->assertStatus(401);
    }
}
