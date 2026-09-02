<?php

namespace App\Http\Controllers;

use App\Models\Rak;
use App\Models\StockOpname;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StockOpnameController extends Controller
{
    public function index()
    {
        $raks = Rak::with(['buku', 'stockOpname.staff'])->get();

        return view('stockopname.index', [
            'raks' => $raks,
        ]);
    }

    public function create(Rak $rak)
    {
        $jumlahTercatat = $rak->buku()->count();

        return view('stock-opname.create', [
            'rak' => $rak,
            'jumlahTercatat' => $jumlahTercatat,
        ]);
    }

    
    public function store(Request $request, Rak $rak)
    {
        $validated = $request->validate([
            'jumlah_fisik' => 'required|integer|min:0',
        ]);

        $staff = Auth::user();

        $opname = StockOpname::lakukanOpname(
            $rak,
            $staff,
            $validated['jumlah_fisik']
        );

        
        if ($opname->selisih !== 0) {
            $pesan = "Stock opname selesai. Ditemukan selisih {$opname->selisih} buku pada rak {$rak->kode_rak}, perlu ditindaklanjuti.";
        } else {
            $pesan = "Stock opname selesai. Jumlah tercatat dan fisik pada rak {$rak->kode_rak} sudah sesuai.";
        }

        return redirect()
            ->route('stock-opname.riwayat', $rak)
            ->with('sukses', $pesan);
    }

   
    public function riwayat(Rak $rak)
    {
        $riwayat = StockOpname::getRiwayatOpname($rak->id);

        return view('stock-opname.riwayat', [
            'rak' => $rak,
            'riwayat' => $riwayat,
        ]);
    }
}
