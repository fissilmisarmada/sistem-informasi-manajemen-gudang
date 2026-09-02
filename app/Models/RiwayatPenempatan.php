<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiwayatPenempatan extends Model
{
    use HasFactory;

    protected $table = 'riwayat_penempatan';

    
    public $timestamps = false;

    protected $fillable = [
        'buku_id',
        'rak_id',
        'staff_id', 
        'tanggal',
    ];

    protected $casts = [
        'tanggal' => 'datetime',
    ];

    public function buku()
    {
        return $this->belongsTo(Buku::class, 'buku_id');
    }

    public function rak()
    {
        return $this->belongsTo(Rak::class, 'rak_id');
    }

    // staff yang melakukan penempatan (tetap merujuk ke tabel users)
    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    // Catat satu baris riwayat baru (dipakai di BukuController)
    public static function catat(Buku $buku, Rak $rak, User $staff): self
    {
        return static::create([
            'buku_id' => $buku->id,
            'rak_id'  => $rak->id,
            'staff_id' => $staff->id,
            'tanggal' => now(),
        ]);
    }

    public static function getByBuku(int $bukuId)
    {
        return static::where('buku_id', $bukuId)->latest('tanggal')->get();
    }

    public static function getByRak(int $rakId)
    {
        return static::where('rak_id', $rakId)->latest('tanggal')->get();
    }
}
