<?php

namespace Tests\Feature;

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

    public function test_users_and_riwayat_pages_are_available_for_authenticated_user(): void
    {
        $user = \App\Models\User::factory()->create([
            'role' => 'admin',
        ]);

        $this->actingAs($user)
            ->get(route('users'))
            ->assertStatus(200);

        $this->actingAs($user)
            ->get(route('riwayat.index'))
            ->assertStatus(200);
    }

    public function test_admin_can_create_and_delete_another_user(): void
    {
        $admin = \App\Models\User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->post(route('users.store'), [
                'name' => 'User Baru',
                'email' => 'user.baru@example.com',
                'role' => 'staff',
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ])
            ->assertRedirect(route('users'));

        $newUser = \App\Models\User::where('email', 'user.baru@example.com')->firstOrFail();

        $this->actingAs($admin)
            ->delete(route('users.destroy', $newUser))
            ->assertRedirect(route('users'));

        $this->assertDatabaseMissing('users', ['email' => 'user.baru@example.com']);
    }

    public function test_admin_cannot_delete_the_active_account(): void
    {
        $admin = \App\Models\User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->delete(route('users.destroy', $admin))
            ->assertSessionHasErrors('user');

        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    public function test_staff_can_place_an_unassigned_book_on_a_rack(): void
    {
        $staff = \App\Models\User::factory()->create(['role' => 'staff']);
        $rak = \App\Models\Rak::create([
            'kode_rak' => 'RAK-01',
            'nama_lokasi' => 'Ruang Utama',
            'kapasitas' => 20,
        ]);
        $buku = \App\Models\Buku::create([
            'kode_buku' => 'BK-001',
            'judul' => 'Buku Uji Penempatan',
            'isbn' => '9780000000001',
        ]);

        $this->actingAs($staff)
            ->get(route('buku.create'))
            ->assertOk()
            ->assertSee('Penempatan Buku Masuk');

        $this->actingAs($staff)
            ->post(route('buku.store'), [
                'buku_id' => $buku->id,
                'rak_id' => $rak->id,
                'jumlah_masuk' => 1,
            ])
            ->assertRedirect(route('buku.create'));

        $this->assertDatabaseHas('buku', [
            'id' => $buku->id,
            'rak_id' => $rak->id,
            'stok' => 1,
        ]);
        $this->assertDatabaseHas('riwayat_penempatan', [
            'buku_id' => $buku->id,
            'rak_id' => $rak->id,
            'staff_id' => $staff->id,
        ]);
    }

    public function test_staff_can_add_a_rack_but_cannot_delete_one(): void
    {
        $staff = \App\Models\User::factory()->create(['role' => 'staff']);

        $this->actingAs($staff)
            ->get(route('rak.index'))
            ->assertOk()
            ->assertSee('Tambah Rak');

        $this->actingAs($staff)
            ->post(route('rak.store'), [
                'kode_rak' => 'RAK-STAFF',
                'nama_lokasi' => 'Ruang Staff',
                'kapasitas' => 15,
            ])
            ->assertRedirect(route('rak.index'));

        $rak = \App\Models\Rak::where('kode_rak', 'RAK-STAFF')->firstOrFail();

        $this->actingAs($staff)
            ->delete(route('rak.destroy', $rak))
            ->assertForbidden();

        $this->assertDatabaseHas('rak', ['id' => $rak->id]);
    }

    public function test_pimpinan_can_view_report_but_cannot_import_or_export(): void
    {
        $pimpinan = \App\Models\User::factory()->create(['role' => 'pimpinan']);

        $this->actingAs($pimpinan)
            ->get(route('laporan.index'))
            ->assertOk()
            ->assertSee('Laporan Perpustakaan');

        $this->actingAs($pimpinan)
            ->get(route('laporan.export'))
            ->assertForbidden();
    }

    public function test_staff_can_export_and_import_book_report(): void
    {
        $staff = \App\Models\User::factory()->create(['role' => 'staff']);
        $csv = implode("\n", [
            'kode_buku,judul,isbn,eisbn,jumlah_halaman,kode_rak,nama_lokasi',
            'BK-IMPORT,Buku Import,9780000000099,,120,,',
        ]);

        $this->actingAs($staff)
            ->get(route('laporan.export'))
            ->assertOk()
            ->assertHeader('content-type', 'text/csv; charset=UTF-8');

        $this->actingAs($staff)
            ->post(route('laporan.import'), [
                'file' => \Illuminate\Http\UploadedFile::fake()->createWithContent('laporan.csv', $csv),
            ])
            ->assertRedirect(route('laporan.index'));

        $this->assertDatabaseHas('buku', [
            'kode_buku' => 'BK-IMPORT',
            'judul' => 'Buku Import',
            'isbn' => '9780000000099',
        ]);
    }

    public function test_staff_can_view_and_create_a_book_with_catalog_fields(): void
    {
        $staff = \App\Models\User::factory()->create(['role' => 'staff']);
        $buku = \App\Models\Buku::create([
            'kode_buku' => 'BK-CATALOG',
            'judul' => 'Buku Katalog',
            'isbn' => '9780000000088',
            'kategori' => 'Teknologi Informasi',
            'stok' => 24,
        ]);

        $this->actingAs($staff)
            ->get(route('buku.show', $buku))
            ->assertOk()
            ->assertSee('Teknologi Informasi')
            ->assertSee('24 Buku');

        $this->actingAs($staff)
            ->post(route('buku.store-data'), [
                'kode_buku' => 'BK-CATALOG-2',
                'judul' => 'Buku Katalog Dua',
                'isbn' => '9780000000077',
                'kategori' => 'Manajemen',
                'stok' => 12,
            ])
            ->assertRedirect(route('buku.index'));

        $this->assertDatabaseHas('buku', [
            'kode_buku' => 'BK-CATALOG-2',
            'kategori' => 'Manajemen',
            'stok' => 12,
        ]);
    }

    public function test_authenticated_user_can_search_books_by_rack_or_isbn(): void
    {
        $staff = \App\Models\User::factory()->create(['role' => 'staff']);
        $rak = \App\Models\Rak::create([
            'kode_rak' => 'RAK-CARI',
            'nama_lokasi' => 'Ruang Referensi',
        ]);
        $buku = \App\Models\Buku::create([
            'kode_buku' => 'BK-CARI',
            'judul' => 'Buku Pencarian',
            'isbn' => '9780000000066',
            'stok' => 8,
            'rak_id' => $rak->id,
        ]);

        $this->actingAs($staff)
            ->get(route('buku.cari', ['q' => 'RAK-CARI']))
            ->assertOk()
            ->assertSee('Buku Pencarian');

        $this->actingAs($staff)
            ->get(route('buku.hasil-cari', ['q' => $buku->isbn]))
            ->assertOk()
            ->assertJsonPath('ditemukan', true)
            ->assertJsonPath('buku.id', $buku->id);
    }

    public function test_staff_can_add_incoming_stock_to_a_book_and_rack(): void
    {
        $staff = \App\Models\User::factory()->create(['role' => 'staff']);
        $rak = \App\Models\Rak::create([
            'kode_rak' => 'RAK-STOK',
            'nama_lokasi' => 'Ruang Stok',
            'kapasitas' => 20,
        ]);
        $buku = \App\Models\Buku::create([
            'kode_buku' => 'BK-STOK',
            'judul' => 'Buku Stok Masuk',
            'isbn' => '9780000000055',
            'stok' => 4,
        ]);

        $this->actingAs($staff)
            ->get(route('buku.create'))
            ->assertOk()
            ->assertSee('Penempatan Buku Masuk');

        $this->actingAs($staff)
            ->post(route('buku.store'), [
                'buku_id' => $buku->id,
                'rak_id' => $rak->id,
                'jumlah_masuk' => 5,
            ])
            ->assertRedirect(route('buku.create'));

        $this->assertDatabaseHas('buku', ['id' => $buku->id, 'rak_id' => $rak->id, 'stok' => 9]);
        $this->assertDatabaseHas('riwayat_penempatan', ['buku_id' => $buku->id, 'rak_id' => $rak->id, 'staff_id' => $staff->id]);
    }
}