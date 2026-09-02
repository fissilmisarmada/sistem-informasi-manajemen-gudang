<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Kategori;
use Illuminate\Http\Request;

class PencarianController extends Controller
{
    /**
     * Pencarian global: mencari di semua barang gudang (termasuk buku)
     * Buku disimpan sebagai Barang dengan kategori_id = 'BKU'
     */
    public function index(Request $request)
    {
        $q          = $request->input('q', '');
        $kategori_id = $request->input('kategori_id');
        $tipe       = $request->input('tipe', 'semua'); // semua | buku | barang

        // Ambil kategori Buku
        $bukuKategori = Kategori::where('kode_kategori', 'BKU')->first();
        $bukuKategoriId = $bukuKategori?->id;

        $query = Barang::with(['kategori', 'rak']);

        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('nama', 'like', "%{$q}%")
                    ->orWhere('kode_barang', 'like', "%{$q}%");
            });
        }

        // Filter berdasarkan tipe (buku/barang/semua)
        if ($tipe === 'buku' && $bukuKategoriId) {
            $query->where('kategori_id', $bukuKategoriId);
        } elseif ($tipe === 'barang' && $bukuKategoriId) {
            $query->where('kategori_id', '!=', $bukuKategoriId);
        }

        // Filter berdasarkan kategori
        if ($kategori_id) {
            $query->where('kategori_id', $kategori_id);
        }

        $barang = $query->orderBy('nama')->get();

        // Pisahkan buku dan barang lainnya untuk penghitungan di view
        $buku = $barang->filter(fn($item) => $item->kategori_id == $bukuKategoriId);
        $barangLain = $barang->filter(fn($item) => $item->kategori_id != $bukuKategoriId);

        $kategoris   = Kategori::orderBy('nama')->get();
        $totalHasil  = $barang->count();

        return view('pencarian.index', compact('buku', 'barangLain', 'barang', 'kategoris', 'q', 'tipe', 'kategori_id', 'totalHasil'));
    }
}

