<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menjalankan migration (membuat tabel).
     */
    public function up(): void
    {
        Schema::create('rak', function (Blueprint $table) {
            $table->id(); // otomatis jadi kolom "id", tipe BIGINT, Primary Key, auto increment
            $table->string('kode_rak', 20)->unique(); // kode rak harus unik, gak boleh dobel
            $table->string('nama_lokasi', 100);
            $table->integer('kapasitas')->nullable(); // boleh kosong dulu kalau belum ditentukan
            $table->timestamps(); // otomatis bikin kolom created_at & updated_at
        });
    }

    /**
     * Membatalkan migration (menghapus tabel).
     */
    public function down(): void
    {
        Schema::dropIfExists('rak');
    }
};
