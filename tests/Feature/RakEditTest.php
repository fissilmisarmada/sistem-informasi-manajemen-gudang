<?php

namespace Tests\Feature;

use App\Models\DenahArea;
use App\Models\Rak;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RakEditTest extends TestCase
{
    use RefreshDatabase;

    private function user(string $role): User
    {
        return User::create([
            'name' => ucfirst($role),
            'email' => $role.'-rak@gmail.com',
            'password' => 'rahasia123',
            'role' => $role,
        ]);
    }

    private function rak(array $attrs = []): Rak
    {
        return Rak::create(array_merge([
            'kode_rak' => 'R-01',
            'nama_lokasi' => 'Area ATK',
            'kapasitas' => 50,
        ], $attrs));
    }

    public function test_admin_dapat_mengubah_kapasitas_rak(): void
    {
        $rak = $this->rak();

        $this->actingAs($this->user('admin'))
            ->put(route('rak.update', $rak), [
                'kode_rak' => 'R-01',
                'nama_lokasi' => 'Area ATK',
                'kapasitas' => 120,
            ])
            ->assertRedirect(route('rak.index'))
            ->assertSessionHas('sukses');

        $this->assertSame(120, (int) $rak->refresh()->kapasitas);
    }

    public function test_admin_dapat_mengubah_kode_lokasi_dan_area_sekaligus(): void
    {
        $area = DenahArea::create(['kode_area' => 'GD-MODUL', 'nama' => 'Ruang modul']);
        $rak = $this->rak();

        $this->actingAs($this->user('admin'))
            ->put(route('rak.update', $rak), [
                'kode_rak' => 'R-99',
                'nama_lokasi' => 'Area Baru',
                'kapasitas' => 77,
                'denah_area_id' => $area->id,
            ]);

        $rak->refresh();
        $this->assertSame('R-99', $rak->kode_rak);
        $this->assertSame('Area Baru', $rak->nama_lokasi);
        $this->assertSame(77, (int) $rak->kapasitas);
        $this->assertSame($area->id, $rak->denah_area_id);
    }

    public function test_area_denah_tidak_hilang_saat_form_mengirim_area_yang_sama(): void
    {
        // Risiko: denah_area_id nullable, form yang tak mengirimnya bisa melepas relasi
        $area = DenahArea::create(['kode_area' => 'GD-ARSIP1', 'nama' => 'Ruang arsip 1']);
        $rak = $this->rak(['denah_area_id' => $area->id]);

        $this->actingAs($this->user('admin'))
            ->put(route('rak.update', $rak), [
                'kode_rak' => 'R-01',
                'nama_lokasi' => 'Area ATK',
                'kapasitas' => 60,
                'denah_area_id' => $area->id,
            ]);

        $this->assertSame($area->id, $rak->refresh()->denah_area_id, 'relasi area harus tetap utuh');
    }

    public function test_area_denah_dapat_dikosongkan(): void
    {
        $area = DenahArea::create(['kode_area' => 'GD-ARSIP2', 'nama' => 'Ruang arsip 2']);
        $rak = $this->rak(['denah_area_id' => $area->id]);

        $this->actingAs($this->user('admin'))
            ->put(route('rak.update', $rak), [
                'kode_rak' => 'R-01',
                'nama_lokasi' => 'Area ATK',
                'kapasitas' => 60,
                'denah_area_id' => '',
            ]);

        $this->assertNull($rak->refresh()->denah_area_id);
    }

    public function test_kapasitas_boleh_dikosongkan(): void
    {
        $rak = $this->rak();

        $this->actingAs($this->user('admin'))
            ->put(route('rak.update', $rak), [
                'kode_rak' => 'R-01',
                'nama_lokasi' => 'Area ATK',
                'kapasitas' => '',
            ]);

        $this->assertNull($rak->refresh()->kapasitas);
    }

    public function test_kapasitas_negatif_ditolak(): void
    {
        $rak = $this->rak();

        $this->actingAs($this->user('admin'))
            ->put(route('rak.update', $rak), [
                'kode_rak' => 'R-01',
                'nama_lokasi' => 'Area ATK',
                'kapasitas' => -5,
            ])
            ->assertSessionHasErrors('kapasitas');

        $this->assertSame(50, (int) $rak->refresh()->kapasitas);
    }

    public function test_kode_rak_duplikat_ditolak(): void
    {
        $this->rak(['kode_rak' => 'R-01']);
        $rak2 = $this->rak(['kode_rak' => 'R-02']);

        $this->actingAs($this->user('admin'))
            ->put(route('rak.update', $rak2), [
                'kode_rak' => 'R-01',
                'nama_lokasi' => 'Area ATK',
                'kapasitas' => 10,
            ])
            ->assertSessionHasErrors('kode_rak');
    }

    public function test_kode_rak_sendiri_tidak_dianggap_duplikat(): void
    {
        $rak = $this->rak(['kode_rak' => 'R-01']);

        $this->actingAs($this->user('admin'))
            ->put(route('rak.update', $rak), [
                'kode_rak' => 'R-01',
                'nama_lokasi' => 'Area ATK diubah',
                'kapasitas' => 10,
            ])
            ->assertSessionHasNoErrors();

        $this->assertSame('Area ATK diubah', $rak->refresh()->nama_lokasi);
    }

    public function test_staff_tidak_boleh_mengubah_rak(): void
    {
        $rak = $this->rak();

        $this->actingAs($this->user('staff'))
            ->put(route('rak.update', $rak), [
                'kode_rak' => 'R-01',
                'nama_lokasi' => 'Area ATK',
                'kapasitas' => 999,
            ])
            ->assertForbidden();

        $this->assertSame(50, (int) $rak->refresh()->kapasitas, 'staff tidak boleh mengubah kapasitas');
    }

    public function test_pimpinan_tidak_boleh_mengubah_rak(): void
    {
        $rak = $this->rak();

        $this->actingAs($this->user('pimpinan'))
            ->put(route('rak.update', $rak), [
                'kode_rak' => 'R-01',
                'nama_lokasi' => 'Area ATK',
                'kapasitas' => 999,
            ])
            ->assertForbidden();

        $this->assertSame(50, (int) $rak->refresh()->kapasitas);
    }

    public function test_tombol_edit_hanya_tampil_untuk_admin(): void
    {
        $rak = $this->rak();

        $html = $this->actingAs($this->user('admin'))->get(route('rak.index'))->assertOk()->getContent();
        $this->assertStringContainsString('id="edit-rak-'.$rak->id.'"', $html, 'admin harus melihat form edit');
        $this->assertStringContainsString('data-rak-toggle', $html, 'admin harus melihat tombol edit');

        foreach (['staff', 'pimpinan'] as $role) {
            $html = $this->actingAs($this->user($role))->get(route('rak.index'))->assertOk()->getContent();

            $this->assertStringNotContainsString('id="edit-rak-'.$rak->id.'"', $html, "$role tidak boleh melihat form edit");
            $this->assertStringNotContainsString('data-rak-toggle', $html, "$role tidak boleh melihat tombol edit");
            $this->assertStringNotContainsString('name="_method" value="PUT"', $html);
        }
    }

    public function test_form_edit_tampil_di_halaman_detail_rak_hanya_untuk_admin(): void
    {
        $rak = $this->rak();

        $this->actingAs($this->user('admin'))
            ->get(route('rak.show', $rak))
            ->assertOk()
            ->assertSee('id="edit-rak-form"', false)
            ->assertSee(route('rak.update', $rak), false);

        foreach (['staff', 'pimpinan'] as $role) {
            $this->actingAs($this->user($role))
                ->get(route('rak.show', $rak))
                ->assertOk()
                ->assertDontSee('id="edit-rak-form"', false)
                ->assertDontSee(route('rak.update', $rak), false);
        }
    }
}
