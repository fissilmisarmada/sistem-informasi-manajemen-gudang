<?php

namespace Tests\Feature;

use App\Models\Barang;
use App\Models\Kategori;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BarangGambarTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::create([
            'name' => 'Admin Uji',
            'email' => 'admin-uji@gmail.com',
            'password' => 'rahasia123',
            'role' => 'admin',
        ]);
    }

    private function barang(?string $gambar = null): Barang
    {
        $kategori = Kategori::create([
            'nama' => 'Furnitur',
            'kode_kategori' => 'FRN',
        ]);

        return Barang::create([
            'kode_barang' => 'UJI-001',
            'nama' => 'Kursi Lipat',
            'kategori_id' => $kategori->id,
            'satuan' => 'unit',
            'stok' => 5,
            'stok_minimum' => 0,
            'gambar' => $gambar,
        ]);
    }

    private function payload(Barang $barang, array $extra = []): array
    {
        return array_merge([
            'kode_barang' => $barang->kode_barang,
            'nama' => $barang->nama,
            'kategori_id' => $barang->kategori_id,
            'satuan' => $barang->satuan,
        ], $extra);
    }

    /** PNG 1x1 asli — GD tidak terpasang di mesin ini, jadi bukan fake()->image(). */
    private function pngUpload(string $name): UploadedFile
    {
        $bytes = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8DwHwAFAAH/q842iQAAAABJRU5ErkJggg==');

        return UploadedFile::fake()->createWithContent($name, $bytes);
    }

    public function test_hapus_gambar_mengosongkan_kolom_dan_menghapus_file(): void
    {
        Storage::fake('public');
        $path = $this->pngUpload('lama.png')->store('barang', 'public');
        $barang = $this->barang($path);
        Storage::disk('public')->assertExists($path);

        $this->actingAs($this->admin())
            ->put(route('barang.update', $barang), $this->payload($barang, ['hapus_gambar' => '1']))
            ->assertRedirect(route('barang.show', $barang));

        $this->assertNull($barang->refresh()->gambar);
        Storage::disk('public')->assertMissing($path);
    }

    public function test_hapus_gambar_menang_atas_upload_baru(): void
    {
        Storage::fake('public');
        $path = $this->pngUpload('lama.png')->store('barang', 'public');
        $barang = $this->barang($path);

        $this->actingAs($this->admin())
            ->put(route('barang.update', $barang), $this->payload($barang, [
                'hapus_gambar' => '1',
                'gambar' => $this->pngUpload('baru.png'),
            ]));

        $this->assertNull($barang->refresh()->gambar, 'hapus harus menang saat dua-duanya dikirim');
        Storage::disk('public')->assertMissing($path);
        $this->assertCount(0, Storage::disk('public')->allFiles('barang'), 'file baru tidak boleh tersimpan');
    }

    public function test_upload_baru_mengganti_dan_menghapus_file_lama(): void
    {
        Storage::fake('public');
        $path = $this->pngUpload('lama.png')->store('barang', 'public');
        $barang = $this->barang($path);

        $this->actingAs($this->admin())
            ->put(route('barang.update', $barang), $this->payload($barang, [
                'gambar' => $this->pngUpload('baru.png'),
            ]));

        $baru = $barang->refresh()->gambar;
        $this->assertNotNull($baru);
        $this->assertNotSame($path, $baru);
        Storage::disk('public')->assertMissing($path);
        Storage::disk('public')->assertExists($baru);
    }

    public function test_tanpa_gambar_maupun_centang_gambar_tidak_berubah(): void
    {
        Storage::fake('public');
        $path = $this->pngUpload('lama.png')->store('barang', 'public');
        $barang = $this->barang($path);

        $this->actingAs($this->admin())
            ->put(route('barang.update', $barang), $this->payload($barang));

        $this->assertSame($path, $barang->refresh()->gambar);
        Storage::disk('public')->assertExists($path);
    }
}
