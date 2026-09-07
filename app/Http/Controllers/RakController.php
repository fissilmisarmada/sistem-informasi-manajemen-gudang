<?php

namespace App\Http\Controllers;

use App\Models\Rak;
use App\Models\Barang;
use Illuminate\Http\Request;

class RakController extends Controller
{

    public function index()
    {
        $dataRak = Rak::withCount('barang')->withSum('barang', 'stok')->orderBy('kode_rak')->get();

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
        $barangDiRakIni = $rak->barang()->with('kategori')->orderBy('nama')->get();

        return view('rak.show', [
            'rak'  => $rak,
            'barang' => $barangDiRakIni,
        ]);
    }

    public function assignBarang(Request $request)
    {
        $validated = $request->validate([
            'barang_id' => 'required|exists:barang,id',
            'rak_id' => 'required|exists:rak,id',
        ]);

        Barang::whereKey($validated['barang_id'])->update(['rak_id' => $validated['rak_id']]);

        return redirect()
            ->route('denah-gudang', ['rak' => $validated['rak_id']])
            ->with('sukses', 'Lokasi barang berhasil diperbarui.');
    }

    public function denahGudang(Request $request)
    {
        $search = trim((string) $request->input('q', ''));
        $statusFilter = $request->input('status', 'semua');
        $dataRak = Rak::withSum('barang', 'stok')
            ->withCount('barang')
            ->orderBy('kode_rak')
            ->get();

        $getStatus = static function (Rak $rak): string {
            $stok = (int) ($rak->barang_sum_stok ?? 0);

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
            $selectedRak->load('barang.kategori');
        }

        $barangTersedia = Barang::with('kategori')
            ->where(function ($query) use ($selectedRak) {
                $query->whereNull('rak_id');
                if ($selectedRak) {
                    $query->orWhere('rak_id', '!=', $selectedRak->id);
                }
            })
            ->orderBy('nama')
            ->get();

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
            'barangTersedia' => $barangTersedia,
        ]);
    }
}
