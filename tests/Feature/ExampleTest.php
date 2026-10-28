<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

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

    public function test_user_can_login_with_email_and_password_and_redirects_to_role_dashboard(): void
    {
        $password = 'password123';
        $admin = User::factory()->create(['email' => 'admin.test@gmail.com', 'role' => 'admin', 'password' => bcrypt($password)]);
        $staff = User::factory()->create(['email' => 'staff.test@gmail.com', 'role' => 'staff', 'password' => bcrypt($password)]);
        $pimpinan = User::factory()->create(['email' => 'pimpinan.test@gmail.com', 'role' => 'pimpinan', 'password' => bcrypt($password)]);

        $resAdmin = $this->post(route('login.perform'), [
            'email' => 'admin.test@gmail.com',
            'password' => $password,
        ]);
        $resAdmin->assertRedirect(route('dashboard.admin'));
        $this->assertAuthenticatedAs($admin);
        $this->post(route('logout'));

        $resStaff = $this->post(route('login.perform'), [
            'email' => 'staff.test@gmail.com',
            'password' => $password,
        ]);
        $resStaff->assertRedirect(route('dashboard.staff'));
        $this->assertAuthenticatedAs($staff);
        $this->post(route('logout'));

        $resPimpinan = $this->post(route('login.perform'), [
            'email' => 'pimpinan.test@gmail.com',
            'password' => $password,
        ]);
        $resPimpinan->assertRedirect(route('dashboard.pimpinan'));
        $this->assertAuthenticatedAs($pimpinan);
    }

    public function test_login_fails_with_invalid_credentials(): void
    {
        User::factory()->create([
            'email' => 'admin.test@gmail.com',
            'password' => bcrypt('correct-password'),
        ]);

        $response = $this->from(route('login'))->post(route('login.perform'), [
            'email' => 'admin.test@gmail.com',
            'password' => 'wrong-password',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }
}
