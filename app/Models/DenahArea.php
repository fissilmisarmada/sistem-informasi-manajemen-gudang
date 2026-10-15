<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DenahArea extends Model
{
    use HasFactory;

    protected $table = 'denah_area';
    protected $fillable = ['kode_area', 'nama', 'keterangan', 'x', 'y', 'w', 'h', 'warna', 'denah_image'];

    public function rak()
    {
        return $this->hasMany(Rak::class, 'denah_area_id');
    }

    protected $casts = ['x' => 'float', 'y' => 'float', 'w' => 'float', 'h' => 'float'];

    public function barang()
    {
        return $this->hasMany(Barang::class, 'denah_area_id');
    }
}
