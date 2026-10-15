<?php

namespace Database\Seeders;

use App\Models\Barang;
use App\Models\DenahArea;
use App\Models\Kategori;
use App\Models\MutasiBarang;
use App\Models\Rak;
use App\Models\StockOpnameBarang;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call(UserSeeder::class);

        // Gudang = DenahArea (3 ruangan)
        $denahData = [
            ['kode_area' => 'GD-MODUL', 'nama' => 'Ruang modul', 'keterangan' => 'Gudang modul', 'x' => 5, 'y' => 5, 'w' => 28, 'h' => 22, 'warna' => '#1651A4'],
            ['kode_area' => 'GD-ARSIP1', 'nama' => 'Ruang arsip 1', 'keterangan' => 'Gudang arsip 1', 'x' => 36, 'y' => 5, 'w' => 28, 'h' => 22, 'warna' => '#0F7A5A'],
            ['kode_area' => 'GD-ARSIP2', 'nama' => 'Ruang arsip 2', 'keterangan' => 'Gudang arsip 2', 'x' => 67, 'y' => 5, 'w' => 28, 'h' => 22, 'warna' => '#7C3AED'],
        ];
        $denah = collect($denahData)->mapWithKeys(fn(array $d) => [$d['kode_area'] => DenahArea::updateOrCreate(['kode_area' => $d['kode_area']], $d)]);

        $rakToGudangKode = [
            'A1' => 'GD-MODUL',
            'A4' => 'GD-MODUL',
            'A2' => 'GD-ARSIP1',
            'A5' => 'GD-ARSIP1',
            'A3' => 'GD-ARSIP2',
        ];
        $rakData = [
            ['kode_rak' => 'A1', 'nama_lokasi' => 'Ruang modul', 'kapasitas' => 25, 'denah_area_id' => $denah[$rakToGudangKode['A1']]->id],
            ['kode_rak' => 'A2', 'nama_lokasi' => 'Ruang arsip 1', 'kapasitas' => 30, 'denah_area_id' => $denah[$rakToGudangKode['A2']]->id],
            ['kode_rak' => 'A3', 'nama_lokasi' => 'Ruang arsip 2', 'kapasitas' => 25, 'denah_area_id' => $denah[$rakToGudangKode['A3']]->id],
            ['kode_rak' => 'A4', 'nama_lokasi' => 'Ruang modul', 'kapasitas' => 20, 'denah_area_id' => $denah[$rakToGudangKode['A4']]->id],
            ['kode_rak' => 'A5', 'nama_lokasi' => 'Ruang arsip 1', 'kapasitas' => 20, 'denah_area_id' => $denah[$rakToGudangKode['A5']]->id],
        ];

        $rak = collect($rakData)->mapWithKeys(function (array $data) {
            return [$data['kode_rak'] => Rak::updateOrCreate(
                ['kode_rak' => $data['kode_rak']],
                $data
            )];
        });

        $rakToGudang = [
            'A1' => $denah['GD-MODUL']->id,
            'A4' => $denah['GD-MODUL']->id,
            'A2' => $denah['GD-ARSIP1']->id,
            'A5' => $denah['GD-ARSIP1']->id,
            'A3' => $denah['GD-ARSIP2']->id,
        ];
        $rakIdToGudang = [];
        foreach ($rakToGudang as $kode => $gid) {
            $rakIdToGudang[$rak[$kode]->id] = $gid;
        }
        // pastikan rak existing ikut ter-backfill
        foreach ($rak as $r) {
            if (!$r->denah_area_id) {
                $r->update(['denah_area_id' => $rakToGudang[$r->kode_rak] ?? null]);
            }
        }

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
            Barang::updateOrCreate(['kode_barang' => $data['kode_buku']], [
                'nama' => $data['judul'],
                'kategori_id' => $kategori['BKU']->id,
                'satuan' => 'eksemplar',
                'stok' => $data['stok'],
                'stok_minimum' => 10,
                'rak_id' => $data['rak_id'],
                'denah_area_id' => $data['rak_id'] ? ($rakIdToGudang[$data['rak_id']] ?? null) : null,
                'keterangan' => json_encode(['isbn' => $data['isbn'], 'kategori_asli' => $data['kategori'], 'jumlah_halaman' => $data['jumlah_halaman']]),
            ]);
        }

        // Seed Barang (umum) — ponytail: stok_minimum fix FRN=0 lainnya=10
        $barangData = [
            ['kode_barang' => 'ATK-001', 'nama' => 'Kertas HVS A4 (1 rim)', 'kategori_id' => $kategori['ATK']->id, 'satuan' => 'rim', 'stok' => 50, 'stok_minimum' => 10, 'rak_id' => $rak['A1']->id],
            ['kode_barang' => 'ATK-002', 'nama' => 'Ballpoint Hitam', 'kategori_id' => $kategori['ATK']->id, 'satuan' => 'box', 'stok' => 120, 'stok_minimum' => 10, 'rak_id' => $rak['A1']->id],
            ['kode_barang' => 'ATK-003', 'nama' => 'Tinta Print Canon (Hitam)', 'kategori_id' => $kategori['ATK']->id, 'satuan' => 'botol', 'stok' => 8, 'stok_minimum' => 10, 'rak_id' => $rak['A1']->id],
            ['kode_barang' => 'ATK-004', 'nama' => 'Amplop Putih B5', 'kategori_id' => $kategori['ATK']->id, 'satuan' => 'rim', 'stok' => 5, 'stok_minimum' => 10, 'rak_id' => $rak['A1']->id],
            ['kode_barang' => 'KMP-001', 'nama' => 'USB Flash Drive 32GB', 'kategori_id' => $kategori['KMP']->id, 'satuan' => 'pcs', 'stok' => 15, 'stok_minimum' => 10, 'rak_id' => $rak['A2']->id],
            ['kode_barang' => 'KMP-002', 'nama' => 'Kabel LAN Cat5e (100m)', 'kategori_id' => $kategori['KMP']->id, 'satuan' => 'roll', 'stok' => 3, 'stok_minimum' => 10, 'rak_id' => $rak['A2']->id],
            ['kode_barang' => 'KMP-003', 'nama' => 'RAM DDR4 8GB', 'kategori_id' => $kategori['KMP']->id, 'satuan' => 'pcs', 'stok' => 6, 'stok_minimum' => 10, 'rak_id' => $rak['A2']->id],
            ['kode_barang' => 'KMP-004', 'nama' => 'Mouse Wireless', 'kategori_id' => $kategori['KMP']->id, 'satuan' => 'pcs', 'stok' => 12, 'stok_minimum' => 10, 'rak_id' => $rak['A2']->id],
            ['kode_barang' => 'FRN-001', 'nama' => 'Meja Kerja', 'kategori_id' => $kategori['FRN']->id, 'satuan' => 'pcs', 'stok' => 4, 'stok_minimum' => 0, 'rak_id' => $rak['A3']->id],
            ['kode_barang' => 'FRN-002', 'nama' => 'Kursi Kantor', 'kategori_id' => $kategori['FRN']->id, 'satuan' => 'pcs', 'stok' => 8, 'stok_minimum' => 0, 'rak_id' => $rak['A3']->id],
            ['kode_barang' => 'FRN-003', 'nama' => 'Lemari Filing', 'kategori_id' => $kategori['FRN']->id, 'satuan' => 'pcs', 'stok' => 2, 'stok_minimum' => 0, 'rak_id' => $rak['A3']->id],
            ['kode_barang' => 'ELC-001', 'nama' => 'Lampu LED Neon', 'kategori_id' => $kategori['ELC']->id, 'satuan' => 'pcs', 'stok' => 25, 'stok_minimum' => 10, 'rak_id' => $rak['A4']->id],
            ['kode_barang' => 'ELC-002', 'nama' => 'Power Strip 6 Lubang', 'kategori_id' => $kategori['ELC']->id, 'satuan' => 'pcs', 'stok' => 10, 'stok_minimum' => 10, 'rak_id' => $rak['A4']->id],
        ];

        $barang = [];
        foreach ($barangData as $data) {
            $data['denah_area_id'] = $data['rak_id'] ? ($rakIdToGudang[$data['rak_id']] ?? null) : null;
            $barang[$data['kode_barang']] = Barang::updateOrCreate(['kode_barang' => $data['kode_barang']], $data);
        }
        Barang::whereNotNull('rak_id')->whereNull('denah_area_id')->each(function (Barang $b) use ($rakIdToGudang) {
            $b->update(['denah_area_id' => $rakIdToGudang[$b->rak_id] ?? null]);
        });

        $staff = User::where('role', 'staff')->first();
        if (!$staff) return;

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
