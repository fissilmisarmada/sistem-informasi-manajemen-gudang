<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Buku extends Model
{
    use HasFactory;

    protected $table = 'buku';

    protected $fillable = [
        'kode_buku',
        'judul',
        'cover',
        'kategori',
        'isbn',
        'eisbn',
        'jumlah_halaman',
        'stok',
        'rak_id',
    ];

    
    public function rak()
    {
        return $this->belongsTo(Rak::class, 'rak_id');
    }

    
    public function riwayatPenempatan()
    {
        return $this->hasMany(RiwayatPenempatan::class, 'buku_id');
    }

    
    public static function findByIsbn(string $isbn)
    {
        return static::where('isbn', $isbn)->first();
    }

    
    public function tempatkanKeRak(Rak $rak): bool
    {
        $this->rak_id = $rak->id;
        return $this->save();
    }
}
