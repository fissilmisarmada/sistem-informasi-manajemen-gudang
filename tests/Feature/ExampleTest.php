<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Barang;
use App\Models\Kategori;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_root_redirects_to_login(): void
    {
        $response = $this->get('/');

        $response->assertRedirect('/login');
    }

    public function test_login_page_is_accessible(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_basic_dashboards_are_accessible_for_authenticated_users(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $staff = User::factory()->create(['role' => 'staff']);
        $pimpinan = User::factory()->create(['role' => 'pimpinan']);

        $this->actingAs($admin)->get('/dashboard/admin')->assertStatus(200);
        $this->actingAs($staff)->get('/dashboard/staff')->assertStatus(200);
        $this->actingAs($pimpinan)->get('/dashboard/pimpinan')->assertStatus(200);
    }

    public function test_admin_can_access_user_management(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get(route('users'))
            ->assertStatus(200);
    }
}
