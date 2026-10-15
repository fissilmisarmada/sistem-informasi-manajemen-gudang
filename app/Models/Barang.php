<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    use HasFactory;

    protected $table = 'barang';
    // ponytail: kolom stok_minimum dipertahankan (no drop) agar histori valid; jika butuh per-kategori beda → pindah ke kategori.stok_minimum
    public const STOK_MINIMUM_DEFAULT = 10;
    public const STOK_MINIMUM_FURNITURE = 0;

    protected $casts = [
        'keterangan' => 'array',
    ];

    protected $fillable = [
        'kode_barang', 'nama', 'kategori_id', 'satuan',
        'stok', 'stok_minimum', 'rak_id', 'denah_area_id', 'gambar', 'keterangan',
    ];

    public static function stokMinimumForKategori(?string $kodeKategori): int
    {
        return $kodeKategori === 'FRN' ? self::STOK_MINIMUM_FURNITURE : self::STOK_MINIMUM_DEFAULT;
    }

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }

    public function rak()
    {
        return $this->belongsTo(Rak::class, 'rak_id');
    }

    public function denahArea()
    {
        return $this->belongsTo(DenahArea::class, 'denah_area_id');
    }

    public function mutasi()
    {
        return $this->hasMany(MutasiBarang::class, 'barang_id');
    }

    public function stockOpname()
    {
        return $this->hasMany(StockOpnameBarang::class, 'barang_id');
    }

    public function isStokMenipis(): bool
    {
        return $this->stok <= $this->stok_minimum && $this->stok_minimum > 0;
    }

    public function scopeStokMenipis($query)
    {
        return $query->whereRaw('stok <= stok_minimum AND stok_minimum > 0');
    }
}
