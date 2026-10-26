<?php

namespace Tests\Feature;

use App\Models\DenahArea;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DenahAreaTest extends TestCase
{
    use RefreshDatabase;

    private function user(string $role): User
    {
        return User::create([
            'name' => ucfirst($role),
            'email' => $role.'-denah@gmail.com',
            'password' => 'rahasia123',
            'role' => $role,
        ]);
    }

    public function test_admin_dapat_menambah_ruangan(): void
    {
        $this->actingAs($this->user('admin'))
            ->get(route('denah-area.index'))
            ->assertOk();

        $this->post(route('denah-area.store'), [
            'kode_area' => 'GD-A1',
            'nama' => 'Ruang ATK',
            'keterangan' => 'Lantai dasar',
        ])
            ->assertRedirect(route('denah-area.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('denah_area', ['kode_area' => 'GD-A1', 'nama' => 'Ruang ATK']);
    }

    public function test_kode_ruangan_duplikat_ditolak(): void
    {
        DenahArea::create(['kode_area' => 'GD-A1', 'nama' => 'Ruang ATK']);

        $this->actingAs($this->user('admin'))
            ->post(route('denah-area.store'), ['kode_area' => 'GD-A1', 'nama' => 'Ruang lain'])
            ->assertSessionHasErrors('kode_area');

        $this->assertSame(1, DenahArea::count());
    }

    public function test_staff_dan_pimpinan_tidak_boleh_menambah_ruangan(): void
    {
        foreach (['staff', 'pimpinan'] as $role) {
            $this->actingAs($this->user($role))
                ->post(route('denah-area.store'), ['kode_area' => "GD-$role", 'nama' => "Ruang $role"])
                ->assertForbidden();
        }

        $this->assertSame(0, DenahArea::count());
    }

    public function test_tombol_tambah_ruangan_hanya_tampil_untuk_admin(): void
    {
        $html = $this->actingAs($this->user('admin'))->get(route('denah-area.index'))->assertOk()->getContent();
        $this->assertStringContainsString('modal-tambah-ruangan', $html, 'admin harus melihat form tambah ruangan');

        foreach (['staff', 'pimpinan'] as $role) {
            $html = $this->actingAs($this->user($role))->get(route('denah-area.index'))->assertOk()->getContent();
            $this->assertStringNotContainsString('modal-tambah-ruangan', $html, "$role tidak boleh melihat form tambah ruangan");
        }
    }
}
