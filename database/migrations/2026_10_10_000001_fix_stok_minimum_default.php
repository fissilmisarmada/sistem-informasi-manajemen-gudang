<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ponytail: kolom stok_minimum dipertahankan; fix FRN=0 lainnya=10 — upgrade when per-kategori butuh kolom kategori.stok_minimum
        $frnId = DB::table('kategori')->where('kode_kategori', 'FRN')->value('id');
        if ($frnId) {
            DB::table('barang')->where('kategori_id', $frnId)->update(['stok_minimum' => 0]);
            DB::table('barang')->where('kategori_id', '!=', $frnId)->update(['stok_minimum' => 10]);
        } else {
            DB::table('barang')->update(['stok_minimum' => 10]);
        }
    }

    public function down(): void
    {
        // tidak revert — nilai lama tidak dipulihkan
    }
};
