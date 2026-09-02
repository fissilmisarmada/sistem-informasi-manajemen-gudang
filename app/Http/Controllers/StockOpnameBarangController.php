<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\StockOpnameBarang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StockOpnameBarangController extends Controller
{
    public function index()
    {
        $barangs = Barang::with(['kategori', 'rak', 'stockOpname' => function ($q) {
            $q->latest('tanggal')->limit(1);
        }])->orderBy('nama')->get();

        return view('stock-opname-barang.index', compact('barangs'));
    }

    public function create(Barang $barang)
    {
        $barang->load('kategori', 'rak');
        $riwayat = $barang->stockOpname()->with('staff')->latest('tanggal')->take(5)->get();
        return view('stock-opname-barang.create', compact('barang', 'riwayat'));
    }

    public function store(Request $request, Barang $barang)
    {
        $request->validate([
            'jumlah_fisik' => 'required|integer|min:0',
            'keterangan'   => 'nullable|string|max:255',
        ]);

        StockOpnameBarang::lakukanOpname(
            $barang,
            Auth::user(),
            $request->jumlah_fisik,
            $request->keterangan
        );

        return redirect()->route('stock-opname-barang.index')
            ->with('success', "Opname untuk barang '{$barang->nama}' berhasil dicatat.");
    }

    public function riwayat(Barang $barang)
    {
        $barang->load('kategori');
        $riwayat = $barang->stockOpname()->with('staff')->latest('tanggal')->paginate(15);
        return view('stock-opname-barang.riwayat', compact('barang', 'riwayat'));
    }
}
