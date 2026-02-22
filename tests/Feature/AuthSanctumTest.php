<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthSanctumTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
    }

    public function test_login_works_with_valid_credentials(): void
    {
        $response = $this->postJson('/login', [
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['message', 'user'])
            ->assertJsonFragment(['email' => 'admin@example.com'])
            ->assertJsonPath('user.name', 'Administrator');
    }

    public function test_login_fails_with_invalid_credentials(): void
    {
        $response = $this->postJson('/login', [
            'email' => 'admin@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(422);
    }

    public function test_api_user_returns_authenticated_user(): void
    {
        $user = User::where('email', 'admin@example.com')->first();
        $this->assertNotNull($user);

        $response = $this->actingAs($user)
            ->getJson('/api/user');

        $response->assertStatus(200)
            ->assertJsonPath('email', 'admin@example.com')
            ->assertJsonPath('name', 'Administrator')
            ->assertJsonStructure(['roles']);
    }

    public function test_api_user_returns_401_when_unauthenticated(): void
    {
        $response = $this->getJson('/api/user');

        $response->assertStatus(401);
    }

    public function test_admin_user_created_with_correct_data(): void
    {
        $admin = User::where('email', 'admin@example.com')->first();

        $this->assertNotNull($admin);
        $this->assertSame('Administrator', $admin->name);
        $this->assertTrue($admin->hasRole('admin'));
    }

    public function test_logout_works(): void
    {
        $user = User::where('email', 'admin@example.com')->first();
        $this->actingAs($user);

        $response = $this->postJson('/logout');

        $response->assertStatus(200)
            ->assertJsonFragment(['message' => 'Logout successful']);
    }
}
