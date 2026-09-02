<?php

namespace App\Http\Controllers;

use App\Models\Rak;
use Illuminate\Http\Request;

class RakController extends Controller
{
    
    public function index()
    {
        $dataRak = Rak::all();

        return view('rak.index', [
            'dataRak' => $dataRak,
        ]);
    }

 
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_rak'    => 'required|string|max:20|unique:rak,kode_rak',
            'nama_lokasi' => 'required|string|max:100',
            'kapasitas'   => 'nullable|integer|min:0',
        ]);
        

        Rak::create($validated);

        return redirect()
            ->route('rak.index')
            ->with('sukses', 'Data rak berhasil ditambahkan.');
    }

    
    public function update(Request $request, Rak $rak)
    {
        $validated = $request->validate([

            'kode_rak'    => 'required|string|max:20|unique:rak,kode_rak,' . $rak->id,
            'nama_lokasi' => 'required|string|max:100',
            'kapasitas'   => 'nullable|integer|min:0',
        ]);

        $rak->update($validated);

        return redirect()
            ->route('rak.index')
            ->with('sukses', 'Data rak berhasil diperbarui.');
    }

  
    public function destroy(Rak $rak)
    {
        $rak->delete();

        return redirect()
            ->route('rak.index')
            ->with('sukses', 'Data rak berhasil dihapus.');
    }

   
    public function show(Rak $rak)
    {
        $bukuDiRakIni = $rak->buku()->get();

        return view('rak.show', [
            'rak'  => $rak,
            'buku' => $bukuDiRakIni,
        ]);
    }

    public function denahGudang(Request $request)
    {
        $search = trim((string) $request->input('q', ''));
        $statusFilter = $request->input('status', 'semua');
        $dataRak = Rak::withSum('buku', 'stok')
            ->withCount('buku')
            ->orderBy('kode_rak')
            ->get();

        $getStatus = static function (Rak $rak): string {
            $stok = (int) ($rak->buku_sum_stok ?? 0);

            return $stok === 0 ? 'kosong' : ($stok <= 5 ? 'menipis' : 'aman');
        };

        $rakTampil = $dataRak->filter(function (Rak $rak) use ($search, $statusFilter, $getStatus): bool {
            $matchesSearch = $search === ''
                || str_contains(strtolower($rak->kode_rak), strtolower($search))
                || str_contains(strtolower($rak->nama_lokasi), strtolower($search));
            $matchesStatus = $statusFilter === 'semua' || $getStatus($rak) === $statusFilter;

            return $matchesSearch && $matchesStatus;
        })->values();

        $selectedRak = $dataRak->firstWhere('id', (int) $request->input('rak')) ?? $rakTampil->first();
        if ($selectedRak) {
            $selectedRak->load('buku');
        }

        $ringkasan = [
            'total' => $dataRak->count(),
            'aman' => $dataRak->filter(fn (Rak $rak): bool => $getStatus($rak) === 'aman')->count(),
            'menipis' => $dataRak->filter(fn (Rak $rak): bool => $getStatus($rak) === 'menipis')->count(),
            'kosong' => $dataRak->filter(fn (Rak $rak): bool => $getStatus($rak) === 'kosong')->count(),
        ];

        return view('rak.denah', [
            'dataRak' => $rakTampil,
            'selectedRak' => $selectedRak,
            'ringkasan' => $ringkasan,
            'search' => $search,
            'statusFilter' => $statusFilter,
            'getStatus' => $getStatus,
        ]);
    }
}
