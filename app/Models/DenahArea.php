<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DenahArea extends Model
{
    use HasFactory;

    protected $table = 'denah_area';
    protected $fillable = ['kode_area', 'nama', 'keterangan', 'x', 'y', 'w', 'h', 'warna'];

    protected $casts = ['x' => 'float', 'y' => 'float', 'w' => 'float', 'h' => 'float'];

    public function barang()
    {
        return $this->hasMany(Barang::class, 'denah_area_id');
    }
}
