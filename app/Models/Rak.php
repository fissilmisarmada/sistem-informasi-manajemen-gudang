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


    public function barang()
    {
        return $this->hasMany(Barang::class, 'rak_id');
    }

    public function totalItem(): int
    {
        return $this->barang()->count();
    }
}
