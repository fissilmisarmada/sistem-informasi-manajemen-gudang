<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rak', function (Blueprint $table) {
            $table->foreignId('denah_area_id')->nullable()->after('kapasitas')->constrained('denah_area')->nullOnDelete();
        });

        // Backfill: map existing rak.nama_lokasi -> denah_area.nama (Ruang modul / Ruang arsip 1 / Ruang arsip 2)
        $map = [
            'Ruang modul' => 'Ruang modul',
            'Ruang arsip 1' => 'Ruang arsip 1',
            'Ruang arsip 2' => 'Ruang arsip 2',
            // legacy fallback
            'Ruang Sains' => 'Ruang arsip 1',
            'Ruang Sosial' => 'Ruang arsip 2',
            'Ruang Referensi' => 'Ruang modul',
            'Ruang Koleksi Baru' => 'Ruang arsip 1',
        ];
        $areas = \Illuminate\Support\Facades\DB::table('denah_area')->pluck('id', 'nama');
        foreach ($map as $rakLokasi => $areaNama) {
            $areaId = $areas[$areaNama] ?? null;
            if ($areaId) {
                \Illuminate\Support\Facades\DB::table('rak')->where('nama_lokasi', $rakLokasi)->whereNull('denah_area_id')->update(['denah_area_id' => $areaId]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('rak', function (Blueprint $table) {
            $table->dropConstrainedForeignId('denah_area_id');
        });
    }
};
