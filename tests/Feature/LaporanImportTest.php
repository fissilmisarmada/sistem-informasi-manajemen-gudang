<?php

namespace Tests\Feature;

use App\Models\Barang;
use App\Models\Kategori;
use App\Models\Rak;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;
use ZipArchive;

class LaporanImportTest extends TestCase
{
    use RefreshDatabase;

    private function user(string $role): User
    {
        return User::create([
            'name' => ucfirst($role),
            'email' => $role.'-import@gmail.com',
            'password' => 'rahasia123',
            'role' => $role,
        ]);
    }

    private function seedBarang(): void
    {
        $kategori = Kategori::create([
            'kode_kategori' => 'ATK',
            'nama' => 'Alat Tulis',
        ]);

        $rak = Rak::create([
            'kode_rak' => 'R-01',
            'nama_lokasi' => 'Rak A1',
        ]);

        Barang::create([
            'kode_barang' => 'ATK-001',
            'nama' => 'Pulpen',
            'kategori_id' => $kategori->id,
            'satuan' => 'pcs',
            'stok' => 12,
            'stok_minimum' => 10,
            'rak_id' => $rak->id,
            'keterangan' => [
                'isbn' => '978-1',
                'kategori_asli' => 'Alat',
                'jumlah_halaman' => 120,
                'catatan' => 'Tes',
            ],
        ]);
    }

    public function test_admin_bisa_mengimpor_kembali_file_excel_hasil_export(): void
    {
        $this->seedBarang();

        $export = $this->actingAs($this->user('admin'))->get(route('laporan.export'));
        $export->assertOk();

        $path = $export->baseResponse->getFile()->getPathname();
        $this->assertFileExists($path);

        Barang::query()->delete();

        $upload = UploadedFile::fake()->createWithContent('laporan.xlsx', file_get_contents($path));

        $response = $this->post(route('laporan.import'), ['file' => $upload]);

        $response->assertRedirect(route('laporan.index'));
        $response->assertSessionHas('sukses');

        $barang = Barang::where('kode_barang', 'ATK-001')->first();

        $this->assertNotNull($barang);
        $this->assertSame('Pulpen', $barang->nama);
        $this->assertSame(12, (int) $barang->stok);
        $this->assertSame('pcs', $barang->satuan);
        $this->assertSame('R-01', $barang->rak?->kode_rak);
        $this->assertSame('978-1', $barang->keterangan['isbn'] ?? null);
        $this->assertSame('Alat', $barang->keterangan['kategori_asli'] ?? null);
        $this->assertSame(120, $barang->keterangan['jumlah_halaman'] ?? null);
        $this->assertSame('Tes', $barang->keterangan['catatan'] ?? null);
    }

    public function test_import_csv_lama_tetap_bekerja(): void
    {
        Kategori::create([
            'kode_kategori' => 'ELC',
            'nama' => 'Elektronik',
        ]);

        $json = json_encode([
            'isbn' => '978-2',
            'kategori_asli' => 'Buku',
            'jumlah_halaman' => 99,
            'catatan' => 'Dari CSV',
        ], JSON_UNESCAPED_UNICODE);

        $csv = "kode_barang,nama,kode_kategori,kategori,satuan,stok,stok_minimum,kode_rak,keterangan\n"
            ."ELC-001,Lampu LED,ELC,Elektronik,pcs,5,0,,Catatan lama\n"
            .'ELC-002,Buku CSV,ELC,Elektronik,pcs,3,0,,"'.str_replace('"', '""', $json).'"'."\n";

        $response = $this->actingAs($this->user('admin'))->post(route('laporan.import'), [
            'file' => UploadedFile::fake()->createWithContent('laporan.csv', $csv),
        ]);

        $response->assertRedirect(route('laporan.index'));
        $response->assertSessionHas('sukses');

        $barang = Barang::where('kode_barang', 'ELC-001')->first();

        $this->assertNotNull($barang);
        $this->assertSame('Lampu LED', $barang->nama);
        $this->assertSame(5, (int) $barang->stok);
        $this->assertSame('Catatan lama', $barang->keterangan['catatan'] ?? null);

        $barangJson = Barang::where('kode_barang', 'ELC-002')->first();

        $this->assertNotNull($barangJson);
        $this->assertSame('978-2', $barangJson->keterangan['isbn'] ?? null);
        $this->assertSame('Buku', $barangJson->keterangan['kategori_asli'] ?? null);
        $this->assertSame(99, $barangJson->keterangan['jumlah_halaman'] ?? null);
        $this->assertSame('Dari CSV', $barangJson->keterangan['catatan'] ?? null);
    }

    public function test_file_dengan_header_tidak_lengkap_ditolak(): void
    {
        $response = $this->actingAs($this->user('admin'))
            ->from(route('laporan.index'))
            ->post(route('laporan.import'), [
                'file' => UploadedFile::fake()->createWithContent(
                    'bad-format.csv',
                    "invalid_header,wrong_col\n1,2,3\n"
                ),
            ]);

        $response->assertRedirect(route('laporan.index'));
        $response->assertSessionHasErrors('file');
    }

    public function test_file_zip_yang_bukan_excel_ditolak(): void
    {
        $path = tempnam(sys_get_temp_dir(), 'badxlsx_');
        $zip = new ZipArchive;
        $zip->open($path, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        $zip->addFromString('hello.txt', 'bukan excel');
        $zip->close();

        $response = $this->actingAs($this->user('admin'))
            ->from(route('laporan.index'))
            ->post(route('laporan.import'), [
                'file' => new UploadedFile($path, 'bad.xlsx', 'application/zip', null, true),
            ]);

        $response->assertRedirect(route('laporan.index'));
        $response->assertSessionHasErrors('file');

        @unlink($path);
    }

    public function test_staff_tidak_bisa_mengimpor_laporan(): void
    {
        $response = $this->actingAs($this->user('staff'))->post(route('laporan.import'), [
            'file' => UploadedFile::fake()->createWithContent(
                'laporan.csv',
                "kode_barang,nama,kode_kategori\nX-1,Barang X,ATK\n"
            ),
        ]);

        $response->assertForbidden();
    }
}
