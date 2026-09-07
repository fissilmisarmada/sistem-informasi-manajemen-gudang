<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Kategori;
use App\Models\Rak;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BarangController extends Controller
{
    public function index(Request $request)
    {
        $query = Barang::with(['kategori', 'rak']);

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('nama', 'like', "%{$q}%")
                    ->orWhere('kode_barang', 'like', "%{$q}%");
            });
        }

        if ($request->filled('kategori_id')) {
            $query->where('kategori_id', $request->kategori_id);
        }

        if ($request->filled('rak_id')) {
            $query->where('rak_id', $request->rak_id);
        }

        $barangs   = $query->orderBy('nama')->paginate(15)->withQueryString();
        $kategoris = Kategori::orderBy('nama')->get();
        $raks      = Rak::orderBy('kode_rak')->get();

        return view('barang.index', compact('barangs', 'kategoris', 'raks'));
    }

    public function stokMenipis()
    {
        $barangs = Barang::with(['kategori', 'rak'])
            ->whereRaw('stok <= stok_minimum AND stok_minimum > 0')
            ->orderBy('nama')
            ->paginate(15);

        return view('barang.stok-menipis', compact('barangs'));
    }

    public function create()
    {
        $kategoris = Kategori::orderBy('nama')->get();
        $raks      = Rak::orderBy('kode_rak')->get();
        return view('barang.create', compact('kategoris', 'raks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_barang'   => 'required|string|max:100|unique:barang,kode_barang',
            'nama'          => 'required|string|max:255',
            'kategori_id'   => 'required|exists:kategori,id',
            'satuan'        => 'required|string|max:50',
            'stok'          => 'required|integer|min:0',
            'stok_minimum'  => 'required|integer|min:0',
            'rak_id'        => 'nullable|exists:rak,id',
            'gambar'        => 'nullable|image|max:2048',
            'keterangan'    => 'nullable|string',
        ]);

        $data = $request->except('gambar');

        if ($request->hasFile('gambar')) {
            $data['gambar'] = $request->file('gambar')->store('barang', 'public');
        }

        Barang::create($data);

        return redirect()->route('barang.index')->with('success', 'Barang berhasil ditambahkan.');
    }

    public function show(Barang $barang)
    {
        $barang->load(['kategori', 'rak', 'mutasi.staff', 'stockOpname.staff']);
        return view('barang.show', compact('barang'));
    }

    public function edit(Barang $barang)
    {
        $kategoris = Kategori::orderBy('nama')->get();
        $raks      = Rak::orderBy('kode_rak')->get();
        return view('barang.edit', compact('barang', 'kategoris', 'raks'));
    }

    public function update(Request $request, Barang $barang)
    {
        $request->validate([
            'kode_barang'   => 'required|string|max:100|unique:barang,kode_barang,' . $barang->id,
            'nama'          => 'required|string|max:255',
            'kategori_id'   => 'required|exists:kategori,id',
            'satuan'        => 'required|string|max:50',
            'stok_minimum'  => 'required|integer|min:0',
            'rak_id'        => 'nullable|exists:rak,id',
            'gambar'        => 'nullable|image|max:2048',
            'keterangan'    => 'nullable|string',
        ]);

        $data = $request->except('gambar');

        if ($request->hasFile('gambar')) {
            if ($barang->gambar) {
                Storage::disk('public')->delete($barang->gambar);
            }
            $data['gambar'] = $request->file('gambar')->store('barang', 'public');
        }

        $barang->update($data);

        return redirect()->route('barang.show', $barang)->with('success', 'Barang berhasil diperbarui.');
    }

    public function destroy(Barang $barang)
    {
        if ($barang->gambar) {
            Storage::disk('public')->delete($barang->gambar);
        }

        $barang->delete();

        return redirect()->route('barang.index')->with('success', 'Barang berhasil dihapus.');
    }
}
