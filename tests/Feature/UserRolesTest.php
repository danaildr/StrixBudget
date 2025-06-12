<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\RegistrationKey;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserRolesTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_role_methods_work_correctly()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $powerUser = User::factory()->create(['role' => 'power_user']);
        $regularUser = User::factory()->create(['role' => 'user']);

        // Тестваме isAdmin()
        $this->assertTrue($admin->isAdmin());
        $this->assertFalse($powerUser->isAdmin());
        $this->assertFalse($regularUser->isAdmin());

        // Тестваме isPowerUser()
        $this->assertFalse($admin->isPowerUser());
        $this->assertTrue($powerUser->isPowerUser());
        $this->assertFalse($regularUser->isPowerUser());

        // Тестваме canManageRegistrationKeys()
        $this->assertTrue($admin->canManageRegistrationKeys());
        $this->assertTrue($powerUser->canManageRegistrationKeys());
        $this->assertFalse($regularUser->canManageRegistrationKeys());

        // Тестваме canManageUserRoles()
        $this->assertTrue($admin->canManageUserRoles());
        $this->assertFalse($powerUser->canManageUserRoles());
        $this->assertFalse($regularUser->canManageUserRoles());
    }

    public function test_admin_can_update_user_roles()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($admin)
            ->patch("/admin/users/{$user->id}/role", [
                'role' => 'power_user'
            ]);

        $response->assertRedirect();
        $this->assertEquals('power_user', $user->fresh()->role);
    }

    public function test_cannot_create_multiple_admin_users()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($admin)
            ->patch("/admin/users/{$user->id}/role", [
                'role' => 'admin'
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertEquals('user', $user->fresh()->role);
    }

    public function test_cannot_create_admin_registration_key_when_admin_exists()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)
            ->post('/admin/registration-keys/generate', [
                'role' => 'admin',
                'description' => 'Test admin key'
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
        
        $this->assertDatabaseMissing('registration_keys', [
            'role' => 'admin'
        ]);
    }

    public function test_power_user_can_create_registration_keys()
    {
        $powerUser = User::factory()->create(['role' => 'power_user']);

        $response = $this->actingAs($powerUser)
            ->post('/power-user/registration-keys/generate', [
                'role' => 'user',
                'description' => 'Test user key'
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('registration_keys', [
            'role' => 'user',
            'created_by' => $powerUser->id
        ]);
    }

    public function test_power_user_cannot_create_admin_keys()
    {
        $powerUser = User::factory()->create(['role' => 'power_user']);

        $response = $this->actingAs($powerUser)
            ->post('/power-user/registration-keys/generate', [
                'role' => 'admin',
                'description' => 'Test admin key'
            ]);

        // Validation error should redirect back with errors
        $response->assertRedirect();
        $response->assertSessionHasErrors('role');
    }

    public function test_regular_user_cannot_access_admin_functions()
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)
            ->get('/admin');

        $response->assertStatus(403);
    }

    public function test_regular_user_cannot_access_power_user_functions()
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)
            ->get('/power-user/registration-keys');

        $response->assertStatus(403);
    }
}
