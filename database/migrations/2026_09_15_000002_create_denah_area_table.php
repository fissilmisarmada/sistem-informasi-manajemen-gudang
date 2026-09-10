<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('denah_area', function (Blueprint $table) {
            $table->id();
            $table->string('kode_area', 20)->unique();
            $table->string('nama', 100);
            $table->text('keterangan')->nullable();
            $table->decimal('x', 5, 2)->default(5);
            $table->decimal('y', 5, 2)->default(5);
            $table->decimal('w', 5, 2)->default(20);
            $table->decimal('h', 5, 2)->default(15);
            $table->string('warna', 20)->default('#1651A4');
            $table->timestamps();
        });

        Schema::table('barang', function (Blueprint $table) {
            $table->foreignId('denah_area_id')->nullable()->after('rak_id')->constrained('denah_area')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('barang', function (Blueprint $table) {
            $table->dropForeign(['denah_area_id']);
            $table->dropColumn('denah_area_id');
        });
        Schema::dropIfExists('denah_area');
    }
};
