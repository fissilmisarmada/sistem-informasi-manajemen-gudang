<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('denah_area', function (Blueprint $table) {
            $table->string('denah_image')->nullable()->after('warna');
        });
    }

    public function down(): void
    {
        Schema::table('denah_area', function (Blueprint $table) {
            $table->dropColumn('denah_image');
        });
    }
};
