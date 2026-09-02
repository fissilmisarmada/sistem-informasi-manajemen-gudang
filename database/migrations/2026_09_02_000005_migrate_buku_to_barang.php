<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Pastikan kategori 'BKU' ada
        $kategoriId = DB::table('kategori')->where('kode_kategori', 'BKU')->value('id');

        if (!$kategoriId) {
            $kategoriId = DB::table('kategori')->insertGetId([
                'kode_kategori' => 'BKU',
                'nama' => 'Buku',
                'deskripsi' => 'Koleksi buku dan literatur',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Migrasi data Buku ke Barang
        $buku = DB::table('buku')->get();

        foreach ($buku as $b) {
            $exists = DB::table('barang')
                ->where('kode_barang', $b->kode_buku)
                ->exists();

            if (!$exists) {
                DB::table('barang')->insert([
                    'kode_barang' => $b->kode_buku,
                    'nama' => $b->judul,
                    'kategori_id' => $kategoriId,
                    'satuan' => 'eksemplar',
                    'stok' => $b->stok ?? 0,
                    'stok_minimum' => 5,
                    'rak_id' => $b->rak_id,
                    'gambar' => $b->cover,
                    'keterangan' => json_encode([
                        'isbn' => $b->isbn,
                        'eisbn' => $b->eisbn,
                        'jumlah_halaman' => $b->jumlah_halaman,
                        'kategori_asli' => $b->kategori,
                    ]),
                    'created_at' => $b->created_at ?? now(),
                    'updated_at' => $b->updated_at ?? now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Hapus barang yang berasal dari migrasi buku
        $kategoriId = DB::table('kategori')->where('kode_kategori', 'BKU')->value('id');
        if ($kategoriId) {
            DB::table('barang')->where('kategori_id', $kategoriId)->delete();
        }
    }
};
