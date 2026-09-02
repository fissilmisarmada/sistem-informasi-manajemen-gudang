<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockOpnameBarang extends Model
{
    use HasFactory;

    protected $table = 'stock_opname_barang';
    public $timestamps = false;
    protected $fillable = ['barang_id', 'staff_id', 'tanggal', 'jumlah_tercatat', 'jumlah_fisik', 'selisih', 'keterangan'];
    protected $casts = ['tanggal' => 'date'];

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }

    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    public static function lakukanOpname(Barang $barang, User $staff, int $jumlahFisik, ?string $keterangan = null): self
    {
        $jumlahTercatat = $barang->stok;

        return static::create([
            'barang_id'       => $barang->id,
            'staff_id'        => $staff->id,
            'tanggal'         => now()->toDateString(),
            'jumlah_tercatat' => $jumlahTercatat,
            'jumlah_fisik'    => $jumlahFisik,
            'selisih'         => $jumlahTercatat - $jumlahFisik,
            'keterangan'      => $keterangan,
        ]);
    }
}
