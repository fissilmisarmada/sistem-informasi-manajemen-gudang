<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\DenahArea;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DenahAreaController extends Controller
{
    public function index()
    {
        $areas = DenahArea::withCount(['barang', 'rak'])->with('rak.barang')->orderBy('kode_area')->get();
        if (request()->wantsJson()) {
            return response()->json($areas);
        }
        return view('denah-area.index', ['areas' => $areas]);
    }

    public function show(DenahArea $denahArea)
    {
        $denahArea->load(['barang.kategori', 'rak.barang.kategori']);
        if (request()->wantsJson()) {
            return response()->json($denahArea);
        }
        return view('denah-area.show', ['area' => $denahArea]);
    }

    public function uploadDenah(Request $request, DenahArea $denahArea)
    {
        $request->validate(['denah_image' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048']);
        if ($denahArea->denah_image) {
            Storage::disk('public')->delete($denahArea->denah_image);
        }
        $path = $request->file('denah_image')->store('denah-area', 'public');
        $denahArea->update(['denah_image' => $path]);
        if ($request->wantsJson()) {
            return response()->json($denahArea->fresh());
        }
        return back()->with('success', 'Denah ' . $denahArea->nama . ' berhasil diperbarui.');
    }

    public function hapusDenah(Request $request, DenahArea $denahArea)
    {
        if ($denahArea->denah_image) {
            Storage::disk('public')->delete($denahArea->denah_image);
            $denahArea->update(['denah_image' => null]);
        }
        if ($request->wantsJson()) {
            return response()->json(['ok' => true]);
        }
        return back()->with('success', 'Denah ' . $denahArea->nama . ' dihapus.');
    }

    public function store(Request $request)
    {
        $d = $request->validate([
            'kode_area' => 'required|string|max:20|unique:denah_area,kode_area',
            'nama' => 'required|string|max:100',
            'keterangan' => 'nullable|string',
            'x' => 'required|numeric|min:0|max:90',
            'y' => 'required|numeric|min:0|max:90',
            'w' => 'required|numeric|min:5|max:90',
            'h' => 'required|numeric|min:5|max:90',
            'warna' => 'nullable|string|max:20',
        ]);
        $area = DenahArea::create($d);
        return response()->json($area, 201);
    }

    public function update(Request $request, DenahArea $denahArea)
    {
        $d = $request->validate([
            'kode_area' => 'required|string|max:20|unique:denah_area,kode_area,' . $denahArea->id,
            'nama' => 'required|string|max:100',
            'keterangan' => 'nullable|string',
            'x' => 'required|numeric|min:0|max:90',
            'y' => 'required|numeric|min:0|max:90',
            'w' => 'required|numeric|min:5|max:90',
            'h' => 'required|numeric|min:5|max:90',
            'warna' => 'nullable|string|max:20',
        ]);
        $denahArea->update($d);
        return response()->json($denahArea);
    }

    public function destroy(DenahArea $denahArea)
    {
        Barang::where('denah_area_id', $denahArea->id)->update(['denah_area_id' => null]);
        $denahArea->delete();
        return response()->json(['ok' => true]);
    }

    public function assignBarang(Request $request)
    {
        $d = $request->validate(['barang_id' => 'required|exists:barang,id', 'denah_area_id' => 'required|exists:denah_area,id']);
        Barang::whereKey($d['barang_id'])->update(['denah_area_id' => $d['denah_area_id'], 'rak_id' => null]);
        return response()->json(['ok' => true]);
    }

    public function updatePosisi(Request $request, DenahArea $denahArea)
    {
        $d = $request->validate(['x' => 'required|numeric|min:0|max:90', 'y' => 'required|numeric|min:0|max:90', 'w' => 'required|numeric|min:5|max:90', 'h' => 'required|numeric|min:5|max:90']);
        $denahArea->update($d);
        return response()->json($denahArea);
    }
}
