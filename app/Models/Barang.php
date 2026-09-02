<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    use HasFactory;

    protected $table = 'barang';
    protected $fillable = [
        'kode_barang', 'nama', 'kategori_id', 'satuan',
        'stok', 'stok_minimum', 'rak_id', 'gambar', 'keterangan',
    ];

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }

    public function rak()
    {
        return $this->belongsTo(Rak::class, 'rak_id');
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
}
