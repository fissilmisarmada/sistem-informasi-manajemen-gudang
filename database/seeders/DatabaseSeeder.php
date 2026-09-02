<?php

namespace Database\Seeders;

use App\Models\Buku;
use App\Models\Rak;
use App\Models\RiwayatPenempatan;
use App\Models\StockOpname;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $rakData = [
            ['kode_rak' => 'A1', 'nama_lokasi' => 'Ruang Modul', 'kapasitas' => 25],
            ['kode_rak' => 'A2', 'nama_lokasi' => 'Ruang Sains', 'kapasitas' => 30],
            ['kode_rak' => 'A3', 'nama_lokasi' => 'Ruang Sosial', 'kapasitas' => 25],
            ['kode_rak' => 'A4', 'nama_lokasi' => 'Ruang Referensi', 'kapasitas' => 20],
            ['kode_rak' => 'A5', 'nama_lokasi' => 'Ruang Koleksi Baru', 'kapasitas' => 20],
        ];

        $rak = collect($rakData)->mapWithKeys(function (array $data) {
            return [$data['kode_rak'] => Rak::updateOrCreate(
                ['kode_rak' => $data['kode_rak']],
                $data
            )];
        });

        $bukuData = [
            ['kode_buku' => 'SI001', 'judul' => 'Pengantar Sistem Informasi', 'kategori' => 'Teknologi Informasi', 'isbn' => '9786021234001', 'jumlah_halaman' => 220, 'stok' => 12, 'rak_id' => $rak['A1']->id],
            ['kode_buku' => 'SI002', 'judul' => 'Analisis dan Perancangan Sistem', 'kategori' => 'Teknologi Informasi', 'isbn' => '9786021234002', 'jumlah_halaman' => 286, 'stok' => 8, 'rak_id' => $rak['A1']->id],
            ['kode_buku' => 'MN001', 'judul' => 'Manajemen Organisasi Modern', 'kategori' => 'Manajemen', 'isbn' => '9786021234003', 'jumlah_halaman' => 194, 'stok' => 20, 'rak_id' => $rak['A2']->id],
            ['kode_buku' => 'MN002', 'judul' => 'Dasar-Dasar Kepemimpinan', 'kategori' => 'Manajemen', 'isbn' => '9786021234004', 'jumlah_halaman' => 168, 'stok' => 6, 'rak_id' => $rak['A2']->id],
            ['kode_buku' => 'SS001', 'judul' => 'Pengantar Ilmu Sosial', 'kategori' => 'Ilmu Sosial', 'isbn' => '9786021234005', 'jumlah_halaman' => 240, 'stok' => 15, 'rak_id' => $rak['A3']->id],
            ['kode_buku' => 'SS002', 'judul' => 'Metode Penelitian Sosial', 'kategori' => 'Ilmu Sosial', 'isbn' => '9786021234006', 'jumlah_halaman' => 312, 'stok' => 0, 'rak_id' => $rak['A3']->id],
            ['kode_buku' => 'RF001', 'judul' => 'Kamus Istilah Akademik', 'kategori' => 'Referensi', 'isbn' => '9786021234007', 'jumlah_halaman' => 420, 'stok' => 4, 'rak_id' => $rak['A4']->id],
            ['kode_buku' => 'KO001', 'judul' => 'Koleksi Baru Perpustakaan', 'kategori' => 'Umum', 'isbn' => '9786021234008', 'jumlah_halaman' => 128, 'stok' => 3, 'rak_id' => null],
        ];

        foreach ($bukuData as $data) {
            Buku::updateOrCreate(['kode_buku' => $data['kode_buku']], $data);
        }

        $staff = User::where('role', 'staff')->first();
        if (!$staff) {
            return;
        }

        foreach ([
            ['kode_buku' => 'SI001', 'kode_rak' => 'A1', 'tanggal' => '2026-08-20 09:00:00'],
            ['kode_buku' => 'MN001', 'kode_rak' => 'A2', 'tanggal' => '2026-08-21 10:30:00'],
            ['kode_buku' => 'SS001', 'kode_rak' => 'A3', 'tanggal' => '2026-08-22 13:15:00'],
        ] as $data) {
            RiwayatPenempatan::firstOrCreate([
                'buku_id' => Buku::where('kode_buku', $data['kode_buku'])->value('id'),
                'rak_id' => $rak[$data['kode_rak']]->id,
                'staff_id' => $staff->id,
                'tanggal' => $data['tanggal'],
            ]);
        }

        foreach ([
            ['kode_rak' => 'A1', 'tanggal' => '2026-08-23', 'jumlah_tercatat' => 2, 'jumlah_fisik' => 2, 'selisih' => 0],
            ['kode_rak' => 'A2', 'tanggal' => '2026-08-24', 'jumlah_tercatat' => 2, 'jumlah_fisik' => 1, 'selisih' => 1],
            ['kode_rak' => 'A3', 'tanggal' => '2026-08-25', 'jumlah_tercatat' => 2, 'jumlah_fisik' => 2, 'selisih' => 0],
        ] as $data) {
            StockOpname::firstOrCreate([
                'rak_id' => $rak[$data['kode_rak']]->id,
                'staff_id' => $staff->id,
                'tanggal' => $data['tanggal'],
            ], [
                'jumlah_tercatat' => $data['jumlah_tercatat'],
                'jumlah_fisik' => $data['jumlah_fisik'],
                'selisih' => $data['selisih'],
            ]);
        }
    }
}
