<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('riwayat_penempatan');
        Schema::dropIfExists('stock_opname');
        Schema::dropIfExists('buku');
    }

    public function down(): void
    {
        // ponytail: restore via git checkout database/migrations/2025_*_create_buku_table.php && migrate
    }
};
