<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('riwayat_penempatan', function (Blueprint $table) {
            $table->id();

            // Tiga foreign key ini yang bikin tabel ini jadi "pencatat riwayat":
            // nyimpen buku apa, ditaruh di rak mana, dan siapa yang naruh.
            $table->foreignId('buku_id')
                  ->constrained('buku')
                  ->cascadeOnDelete(); // kalau buku dihapus, riwayatnya ikut terhapus

            $table->foreignId('rak_id')
                  ->constrained('rak')
                  ->cascadeOnDelete();

            $table->foreignId('staff_id')
                  ->constrained('users')
                  ->cascadeOnDelete();

            $table->dateTime('tanggal');

            // Sengaja cuma bikin created_at (bukan timestamps() yang bikin dua kolom),
            // soalnya baris riwayat itu gak boleh diubah-ubah lagi setelah dicatat.
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('riwayat_penempatan');
    }
};
