<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('barang', function (Blueprint $table) {
            $table->id();
            $table->string('kode_barang', 100)->unique();
            $table->string('nama', 255);
            $table->foreignId('kategori_id')->constrained('kategori')->restrictOnDelete();
            $table->string('satuan', 50)->default('pcs'); // pcs, rim, unit, dll
            $table->integer('stok')->default(0);
            $table->integer('stok_minimum')->default(0); // alert jika stok di bawah ini
            $table->foreignId('rak_id')->nullable()->constrained('rak')->nullOnDelete();
            $table->string('gambar')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('barang');
    }
};
