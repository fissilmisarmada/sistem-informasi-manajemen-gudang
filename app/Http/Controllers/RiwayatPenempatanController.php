<?php

namespace App\Http\Controllers;

use App\Models\MutasiBarang;
use App\Models\StockOpnameBarang;
use Illuminate\Http\Request;

class RiwayatPenempatanController extends Controller
{
    public function index()
    {
        $mutasi = MutasiBarang::with(['barang', 'staff'])
            ->latest('tanggal')
            ->latest('created_at')
            ->take(30)
            ->get();
        $opname = StockOpnameBarang::with(['barang', 'staff'])
            ->latest('tanggal')
            ->take(30)
            ->get();

        return view('riwayat.index', [
            'mutasi' => $mutasi,
            'opname' => $opname,
        ]);
    }
}
