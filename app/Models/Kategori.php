<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    use HasFactory;

    protected $table = 'kategori';
    protected $fillable = ['kode_kategori', 'nama', 'deskripsi'];

    public function barang()
    {
        return $this->hasMany(Barang::class, 'kategori_id');
    }
}
