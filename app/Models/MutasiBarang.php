<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MutasiBarang extends Model
{
    use HasFactory;

    protected $table = 'mutasi_barang';
    public $timestamps = false;
    protected $fillable = ['barang_id', 'staff_id', 'jenis', 'jumlah', 'keterangan', 'tanggal'];
    protected $casts = ['tanggal' => 'date'];

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }

    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    public static function catat(Barang $barang, User $staff, string $jenis, int $jumlah, ?string $keterangan = null): self
    {
        $mutasi = static::create([
            'barang_id'  => $barang->id,
            'staff_id'   => $staff->id,
            'jenis'      => $jenis,
            'jumlah'     => $jumlah,
            'keterangan' => $keterangan,
            'tanggal'    => now()->toDateString(),
        ]);

        if ($jenis === 'masuk') {
            $barang->increment('stok', $jumlah);
        } else {
            $barang->decrement('stok', $jumlah);
        }

        return $mutasi;
    }
}
