<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Kategori;
use App\Models\MutasiBarang;
use App\Models\Rak;
use App\Models\User;

class DashboardController extends Controller
{
    private function data(): array
    {
        $bukuKategori = Kategori::where('kode_kategori', 'BKU')->first();
        $bukuKategoriId = $bukuKategori?->id;

        return [
            'bukuKategoriId' => $bukuKategoriId,
            'totalBuku' => $bukuKategoriId ? Barang::where('kategori_id', $bukuKategoriId)->count() : 0,
            'totalBarang' => $bukuKategoriId ? Barang::where('kategori_id', '!=', $bukuKategoriId)->count() : Barang::count(),
            'totalItem' => Barang::count(),
            'totalRak' => Rak::count(),
            'totalKategori' => Kategori::count(),
            'totalUser' => User::count(),
            'totalAdmin' => User::where('role', 'admin')->count(),
            'totalStaff' => User::where('role', 'staff')->count(),
            'totalPimpinan' => User::where('role', 'pimpinan')->count(),
            'barangMenipis' => Barang::whereRaw('stok <= stok_minimum AND stok_minimum > 0')->count(),
            'barangMenipis3' => Barang::whereRaw('stok <= stok_minimum AND stok_minimum > 0')->orderBy('nama')->take(3)->get(),
            'aktivitasTerbaru' => MutasiBarang::with(['barang.kategori', 'staff'])->latest('created_at')->take(4)->get(),
        ];
    }

    public function admin()
    {
        return view('dashboard.admin', $this->data());
    }

    public function staff()
    {
        $d = $this->data();
        $bukuKategoriId = $d['bukuKategoriId'];
        $d['barangMenipisBarang'] = $bukuKategoriId ? Barang::whereRaw('stok <= stok_minimum AND stok_minimum > 0')->where('kategori_id', '!=', $bukuKategoriId)->count() : 0;
        $d['bukuMenipis'] = $bukuKategoriId ? Barang::whereRaw('stok <= stok_minimum AND stok_minimum > 0')->where('kategori_id', $bukuKategoriId)->count() : 0;
        $d['totalStokMenipis'] = $d['barangMenipisBarang'] + $d['bukuMenipis'];
        return view('dashboard.staff', $d);
    }

    public function pimpinan()
    {
        return view('dashboard.pimpinan', $this->data());
    }
}
