<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Mutasi barang: mencatat setiap pergerakan barang masuk/keluar gudang
        Schema::create('mutasi_barang', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barang_id')->constrained('barang')->cascadeOnDelete();
            $table->foreignId('staff_id')->constrained('users')->cascadeOnDelete();
            $table->enum('jenis', ['masuk', 'keluar']);
            $table->integer('jumlah');
            $table->string('keterangan')->nullable();
            $table->date('tanggal');
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mutasi_barang');
    }
};
