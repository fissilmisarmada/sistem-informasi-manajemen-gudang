<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_opname', function (Blueprint $table) {
            $table->id();

            $table->foreignId('rak_id')
                  ->constrained('rak')
                  ->cascadeOnDelete();

            $table->foreignId('staff_id')
                  ->constrained('users')
                  ->cascadeOnDelete();

            $table->date('tanggal');
            $table->integer('jumlah_tercatat'); // jumlah versi sistem
            $table->integer('jumlah_fisik');    // jumlah hasil hitung manual
            $table->integer('selisih');         // hasil pengurangan, dihitung otomatis di aplikasi

            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_opname');
    }
};
