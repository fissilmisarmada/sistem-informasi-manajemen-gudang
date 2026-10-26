<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LaporanExportTest extends TestCase
{
    use RefreshDatabase;

    private function user(string $role): User
    {
        return User::create([
            'name' => ucfirst($role),
            'email' => $role.'-laporan@gmail.com',
            'password' => 'rahasia123',
            'role' => $role,
        ]);
    }

    public function test_semua_role_terautentikasi_dapat_mengekspor_laporan(): void
    {
        foreach (['admin', 'staff', 'pimpinan'] as $role) {
            $this->actingAs($this->user($role))
                ->get(route('laporan.export'))
                ->assertOk()
                ->assertHeader('Content-Type', 'text/csv; charset=UTF-8')
                ->assertDownload(null, 'csv');
        }
    }

    public function test_tamu_tidak_bisa_mengekspor_laporan(): void
    {
        $this->get(route('laporan.export'))->assertRedirect(route('login'));
    }

    public function test_staff_melihat_tautan_nav_dan_tombol_export_laporan(): void
    {
        $html = $this->actingAs($this->user('staff'))
            ->get(route('laporan.index'))
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString(route('laporan.export'), $html, 'tombol export harus tampil untuk staff');
        $this->assertStringContainsString('aria-label="Laporan"', $html, 'nav staff harus punya item Laporan');
    }
}
