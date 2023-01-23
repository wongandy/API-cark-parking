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

    public function test_user_can_update_profile()
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)->putJson('api/v1/auth/profile/' . $user->id, [
            'name' => 'barry',
            'email' => 'b@b.com',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['data'])
            ->assertJsonPath('data.name', 'barry')
            ->assertJsonPath('data.email', 'b@b.com');

        $this->assertDatabaseHas('users', [
            'name' => 'barry',
            'email' => 'b@b.com',
        ]);
    }

    public function test_user_can_view_profile()
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)->getJson('api/v1/auth/profile');

        $response->assertStatus(200)
            ->assertJsonStructure(['data'])
            ->assertJsonPath('data.name', $user->name)
            ->assertJsonPath('data.email', $user->email);

        $this->assertDatabaseHas('users', [
            'name' => $user->name,
            'email' => $user->email,
        ]);
    }

    public function test_user_can_update_password()
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)->putJson('api/v1/auth/password', [
            'current_password' => 'password',
            'password' => 'abcdefgh',
            'password_confirmation' => 'abcdefgh',
        ]);

        $updatedPassword = User::first()->password;

        $response->assertStatus(202)
            ->assertJsonStructure(['message'])
            ->assertJsonPath('message', 'Password update successful.');

        $this->assertDatabaseHas('users', [
            'password' => $updatedPassword
        ]);
    }
}
