<?php

namespace App\Http\Controllers;

use App\Models\RiwayatPenempatan;
use Illuminate\Http\Request;

class RiwayatPenempatanController extends Controller
{
    public function index()
    {
        $riwayat = RiwayatPenempatan::with(['buku', 'rak', 'staff'])
            ->latest('tanggal')
            ->get();

        return view('riwayat.index', [
            'riwayat' => $riwayat,
        ]);
    }
}
