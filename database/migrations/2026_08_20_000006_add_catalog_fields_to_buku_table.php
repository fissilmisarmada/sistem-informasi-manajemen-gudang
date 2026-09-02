<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('buku', function (Blueprint $table) {
            $table->string('cover')->nullable()->after('judul');
            $table->string('kategori', 100)->nullable()->after('cover');
            $table->unsignedInteger('stok')->default(0)->after('jumlah_halaman');
        });
    }

    public function down(): void
    {
        Schema::table('buku', function (Blueprint $table) {
            $table->dropColumn(['cover', 'kategori', 'stok']);
        });
    }
};
