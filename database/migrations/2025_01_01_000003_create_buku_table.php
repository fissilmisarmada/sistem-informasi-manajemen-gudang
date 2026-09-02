<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('buku', function (Blueprint $table) {
            $table->id();
            $table->string('kode_buku', 100)->unique();
            $table->string('judul', 255);
            $table->string('isbn', 100)->nullable();
            $table->string('eisbn', 100)->nullable(); // boleh kosong, gak semua buku punya versi digital
            $table->integer('jumlah_halaman')->nullable();

            // rak_id: boleh NULL, karena buku baru (hasil scan/tambah manual)
            // belum tentu langsung punya lokasi rak saat pertama dibuat.
            $table->foreignId('rak_id')
                  ->nullable()
                  ->constrained('rak')      // terhubung ke tabel "rak"
                  ->nullOnDelete();         // kalau rak-nya dihapus, rak_id di buku otomatis jadi NULL (bukan ikut kehapus)

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('buku');
    }
};
