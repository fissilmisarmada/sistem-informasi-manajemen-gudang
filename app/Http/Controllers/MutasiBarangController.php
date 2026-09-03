<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\MutasiBarang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MutasiBarangController extends Controller
{
    public function index(Request $request)
    {
        $query = MutasiBarang::with(['barang.kategori', 'staff'])->latest('created_at');

        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }

        if ($request->filled('barang_id')) {
            $query->where('barang_id', $request->barang_id);
        }

        $mutasis = $query->paginate(20)->withQueryString();
        $barangs = Barang::orderBy('nama')->get();

        return view('mutasi-barang.index', compact('mutasis', 'barangs'));
    }

    public function create()
    {
        $barangs = Barang::with('kategori')->orderBy('nama')->get();
        return view('mutasi-barang.create', compact('barangs'));
    }

    public function store(Request $request)
{
    $request->validate([
        'barang_id'  => 'required|exists:barang,id',
        'jenis'      => 'required|in:masuk,keluar',
        'jumlah'     => 'required|integer|min:1',
        'keterangan' => 'nullable|string|max:255',
    ]);

    $barang = Barang::findOrFail($request->barang_id);

    if ($request->jenis === 'keluar' && $barang->stok < $request->jumlah) {
        return back()->withErrors(['jumlah' => 'Stok tidak mencukupi. Stok saat ini: ' . $barang->stok])->withInput();
    }

    DB::transaction(function () use ($request, $barang) {
        MutasiBarang::catat(
            $barang,
            Auth::user(),
            $request->jenis,
            $request->jumlah,
            $request->keterangan
        );
    });

    // REDIRECT BERDASARKAN BARANG_ID (SIMPLIFY)
    if ($request->filled('barang_id')) {
        return redirect()->route('barang.show', $request->barang_id)
            ->with('success', 'Mutasi barang berhasil dicatat.');
    }

    return redirect()->route('mutasi-barang.index')
        ->with('success', 'Mutasi barang berhasil dicatat.');
    }
}
