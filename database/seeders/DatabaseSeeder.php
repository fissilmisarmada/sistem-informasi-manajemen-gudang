<?php

namespace Database\Seeders;

use App\Models\Barang;
use App\Models\Buku;
use App\Models\Kategori;
use App\Models\MutasiBarang;
use App\Models\Rak;
use App\Models\RiwayatPenempatan;
use App\Models\StockOpname;
use App\Models\StockOpnameBarang;
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

        // Seed Kategori
        $kategoriData = [
            ['kode_kategori' => 'BKU', 'nama' => 'Buku', 'deskripsi' => 'Koleksi buku perpustakaan'],
            ['kode_kategori' => 'ATK', 'nama' => 'Alat Tulis Kantor', 'deskripsi' => 'Alat tulis, kertas, tinta, dll'],
            ['kode_kategori' => 'KMP', 'nama' => 'Komputer & IT', 'deskripsi' => 'Perangkat komputer dan aksesoris'],
            ['kode_kategori' => 'FRN', 'nama' => 'Furniture', 'deskripsi' => 'Meja, kursi, lemari, rak'],
            ['kode_kategori' => 'ELC', 'nama' => 'Elektronik', 'deskripsi' => 'Perangkat elektronik lainnya'],
        ];

        $kategori = collect($kategoriData)->mapWithKeys(function (array $data) {
            return [$data['kode_kategori'] => Kategori::updateOrCreate(
                ['kode_kategori' => $data['kode_kategori']],
                $data
            )];
        });

        // Seed Buku (existing data)
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

        // Seed Barang (umum - bukan buku)
        $barangData = [
            // ATK
            ['kode_barang' => 'ATK-001', 'nama' => 'Kertas HVS A4 (1 rim)', 'kategori_id' => $kategori['ATK']->id, 'satuan' => 'rim', 'stok' => 50, 'stok_minimum' => 10, 'rak_id' => $rak['A1']->id],
            ['kode_barang' => 'ATK-002', 'nama' => 'Ballpoint Hitam', 'kategori_id' => $kategori['ATK']->id, 'satuan' => 'box', 'stok' => 120, 'stok_minimum' => 30, 'rak_id' => $rak['A1']->id],
            ['kode_barang' => 'ATK-003', 'nama' => 'Tinta Print Canon (Hitam)', 'kategori_id' => $kategori['ATK']->id, 'satuan' => 'botol', 'stok' => 8, 'stok_minimum' => 2, 'rak_id' => $rak['A1']->id],
            ['kode_barang' => 'ATK-004', 'nama' => 'Amplop Putih B5', 'kategori_id' => $kategori['ATK']->id, 'satuan' => 'rim', 'stok' => 5, 'stok_minimum' => 5, 'rak_id' => $rak['A1']->id],

            // Komputer & IT
            ['kode_barang' => 'KMP-001', 'nama' => 'USB Flash Drive 32GB', 'kategori_id' => $kategori['KMP']->id, 'satuan' => 'pcs', 'stok' => 15, 'stok_minimum' => 5, 'rak_id' => $rak['A2']->id],
            ['kode_barang' => 'KMP-002', 'nama' => 'Kabel LAN Cat5e (100m)', 'kategori_id' => $kategori['KMP']->id, 'satuan' => 'roll', 'stok' => 3, 'stok_minimum' => 1, 'rak_id' => $rak['A2']->id],
            ['kode_barang' => 'KMP-003', 'nama' => 'RAM DDR4 8GB', 'kategori_id' => $kategori['KMP']->id, 'satuan' => 'pcs', 'stok' => 6, 'stok_minimum' => 2, 'rak_id' => $rak['A2']->id],
            ['kode_barang' => 'KMP-004', 'nama' => 'Mouse Wireless', 'kategori_id' => $kategori['KMP']->id, 'satuan' => 'pcs', 'stok' => 12, 'stok_minimum' => 3, 'rak_id' => $rak['A2']->id],

            // Furniture
            ['kode_barang' => 'FRN-001', 'nama' => 'Meja Kerja', 'kategori_id' => $kategori['FRN']->id, 'satuan' => 'pcs', 'stok' => 4, 'stok_minimum' => 1, 'rak_id' => $rak['A3']->id],
            ['kode_barang' => 'FRN-002', 'nama' => 'Kursi Kantor', 'kategori_id' => $kategori['FRN']->id, 'satuan' => 'pcs', 'stok' => 8, 'stok_minimum' => 2, 'rak_id' => $rak['A3']->id],
            ['kode_barang' => 'FRN-003', 'nama' => 'Lemari Filing', 'kategori_id' => $kategori['FRN']->id, 'satuan' => 'pcs', 'stok' => 2, 'stok_minimum' => 1, 'rak_id' => $rak['A3']->id],

            // Elektronik
            ['kode_barang' => 'ELC-001', 'nama' => 'Lampu LED Neon', 'kategori_id' => $kategori['ELC']->id, 'satuan' => 'pcs', 'stok' => 25, 'stok_minimum' => 5, 'rak_id' => $rak['A4']->id],
            ['kode_barang' => 'ELC-002', 'nama' => 'Power Strip 6 Lubang', 'kategori_id' => $kategori['ELC']->id, 'satuan' => 'pcs', 'stok' => 10, 'stok_minimum' => 2, 'rak_id' => $rak['A4']->id],
        ];

        $barang = [];
        foreach ($barangData as $data) {
            $barang[$data['kode_barang']] = Barang::updateOrCreate(['kode_barang' => $data['kode_barang']], $data);
        }

        // Get staff user
        $staff = User::where('role', 'staff')->first();
        if (!$staff) {
            return;
        }

        // Seed Riwayat Penempatan Buku
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

        // Seed Stock Opname Buku
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

        // Seed Mutasi Barang
        foreach ([
            ['kode_barang' => 'ATK-001', 'jenis' => 'masuk', 'jumlah' => 50, 'keterangan' => 'Pembelian dari supplier', 'tanggal' => '2026-08-20'],
            ['kode_barang' => 'ATK-002', 'jenis' => 'masuk', 'jumlah' => 120, 'keterangan' => 'Pembelian dari toko', 'tanggal' => '2026-08-21'],
            ['kode_barang' => 'ATK-003', 'jenis' => 'masuk', 'jumlah' => 8, 'keterangan' => 'Pembelian tinta printer', 'tanggal' => '2026-08-22'],
            ['kode_barang' => 'KMP-001', 'jenis' => 'masuk', 'jumlah' => 15, 'keterangan' => 'Stock awal', 'tanggal' => '2026-08-19'],
            ['kode_barang' => 'KMP-003', 'jenis' => 'keluar', 'jumlah' => 2, 'keterangan' => 'Untuk upgrade komputer kantor', 'tanggal' => '2026-08-23'],
            ['kode_barang' => 'FRN-001', 'jenis' => 'masuk', 'jumlah' => 4, 'keterangan' => 'Stock awal', 'tanggal' => '2026-08-15'],
            ['kode_barang' => 'ELC-001', 'jenis' => 'masuk', 'jumlah' => 25, 'keterangan' => 'Lampu LED untuk gedung baru', 'tanggal' => '2026-08-18'],
        ] as $data) {
            MutasiBarang::firstOrCreate([
                'barang_id' => $barang[$data['kode_barang']]->id,
                'staff_id' => $staff->id,
                'jenis' => $data['jenis'],
                'jumlah' => $data['jumlah'],
                'keterangan' => $data['keterangan'],
                'tanggal' => $data['tanggal'],
            ]);
        }

        // Seed Stock Opname Barang
        foreach ([
            ['kode_barang' => 'ATK-001', 'tanggal' => '2026-08-26', 'jumlah_tercatat' => 50, 'jumlah_fisik' => 50, 'selisih' => 0],
            ['kode_barang' => 'ATK-002', 'tanggal' => '2026-08-27', 'jumlah_tercatat' => 120, 'jumlah_fisik' => 119, 'selisih' => 1],
            ['kode_barang' => 'KMP-001', 'tanggal' => '2026-08-28', 'jumlah_tercatat' => 15, 'jumlah_fisik' => 15, 'selisih' => 0],
        ] as $data) {
            StockOpnameBarang::firstOrCreate([
                'barang_id' => $barang[$data['kode_barang']]->id,
                'staff_id' => $staff->id,
                'tanggal' => $data['tanggal'],
            ], [
                'jumlah_tercatat' => $data['jumlah_tercatat'],
                'jumlah_fisik' => $data['jumlah_fisik'],
                'selisih' => $data['selisih'],
                'keterangan' => null,
            ]);
        }
    }
}
