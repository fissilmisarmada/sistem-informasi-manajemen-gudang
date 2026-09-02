<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockOpname extends Model
{
    use HasFactory;

    protected $table = 'stock_opname';

    
    public $timestamps = false;

    protected $fillable = [
        'rak_id',
        'staff_id',
        'tanggal',
        'jumlah_tercatat',
        'jumlah_fisik',
        'selisih',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function rak()
    {
        return $this->belongsTo(Rak::class, 'rak_id');
    }

    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    // Lakukan pencatatan hasil stock opname, selisih dihitung otomatis di sini
    public static function lakukanOpname(Rak $rak, User $staff, int $jumlahFisik): self
    {
        $jumlahTercatat = $rak->buku()->count();

        return static::create([
            'rak_id'          => $rak->id,
            'staff_id'        => $staff->id,
            'tanggal'         => now()->toDateString(),
            'jumlah_tercatat' => $jumlahTercatat,
            'jumlah_fisik'    => $jumlahFisik,
            'selisih'         => $jumlahTercatat - $jumlahFisik,
        ]);
    }

    public static function getRiwayatOpname(int $rakId)
    {
        return static::where('rak_id', $rakId)->latest('tanggal')->get();
    }
}
