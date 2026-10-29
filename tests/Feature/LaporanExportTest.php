<?php

namespace Tests\Feature;

use App\Models\Barang;
use App\Models\Kategori;
use App\Models\Rak;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use ZipArchive;

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

    public function test_semua_role_terautentikasi_dapat_mengekspor_laporan_excel(): void
    {
        $this->seedBarang();

        foreach (['admin', 'staff', 'pimpinan'] as $role) {
            $response = $this->actingAs($this->user($role))
                ->get(route('laporan.export'));

            $response->assertOk()
                ->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
                ->assertDownload();

            $this->assertMatchesRegularExpression(
                '/attachment; filename=.*\.xlsx/',
                (string) $response->headers->get('content-disposition')
            );

            $path = $response->baseResponse->getFile()->getPathname();
            $this->assertFileExists($path);
            $this->assertSame('PK', file_get_contents($path, false, null, 0, 2));

            $zip = new ZipArchive;
            $this->assertTrue($zip->open($path) === true, 'File export harus berupa XLSX valid.');

            $workbook = (string) $zip->getFromName('xl/workbook.xml');
            $sheet = (string) $zip->getFromName('xl/worksheets/sheet1.xml');

            $this->assertStringContainsString('Laporan Barang', $workbook);
            $this->assertStringContainsString('Ringkasan', $workbook);
            $this->assertStringContainsString('Kode Barang', $sheet);
            $this->assertStringContainsString('ATK-001', $sheet);
            $this->assertStringContainsString('Pulpen', $sheet);
            $this->assertStringContainsString('<v>12</v>', $sheet);

            $zip->close();
        }
    }

    public function test_csv_tetap_tersedia_untuk_kompatibilitas_impor(): void
    {
        $response = $this->actingAs($this->user('admin'))
            ->get(route('laporan.export.csv'));

        $response->assertOk()
            ->assertHeader('Content-Type', 'text/csv; charset=UTF-8')
            ->assertDownload();

        $this->assertStringContainsString('kode_barang', $response->getContent());
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
        $this->assertStringContainsString(route('laporan.export.csv'), $html, 'tautan CSV harus tampil');
        $this->assertStringContainsString('Export Excel', $html, 'tombol export harus berlabel Excel');
        $this->assertStringContainsString('aria-label="Laporan"', $html, 'nav staff harus punya item Laporan');
    }
}
