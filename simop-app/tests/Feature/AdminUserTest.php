<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUserTest extends TestCase
{
    use RefreshDatabase;
    public function test_admin_can_access_users_page(): void
    {
        $adminRole = Role::firstOrCreate(['slug' => 'admin'], ['name' => 'Administrator']);
        $admin = User::firstOrCreate(
            ['email' => 'admin_test@simop.local'],
            [
                'name' => 'Admin Test',
                'password' => bcrypt('password'),
                'role_id' => $adminRole->id,
            ]
        );

        $response = $this->actingAs($admin)->get('/admin/users');

        $response->assertStatus(200);
        $response->assertSee('Manajemen Pengguna');
    }
}
