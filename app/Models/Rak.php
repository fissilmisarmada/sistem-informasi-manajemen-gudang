<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rak extends Model
{
    use HasFactory;

    protected $table = 'rak';

    protected $fillable = [
        'kode_rak',
        'nama_lokasi',
        'kapasitas',
    ];


    public function buku()
    {
        return $this->hasMany(Buku::class, 'rak_id');
    }


    public function riwayatPenempatan()
    {
        return $this->hasMany(RiwayatPenempatan::class, 'rak_id');
    }

    public function stockOpname()
    {
        return $this->hasMany(StockOpname::class, 'rak_id');
    }

    public function barang()
    {
        return $this->hasMany(Barang::class, 'rak_id');
    }

    // Total semua item di rak ini (buku + barang)
    public function totalItem(): int
    {
        return $this->buku()->count() + $this->barang()->count();
    }
}
