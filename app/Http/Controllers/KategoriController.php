<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    public function index()
    {
        $kategoris = Kategori::withCount('barang')->orderBy('nama')->get();
        return view('kategori.index', compact('kategoris'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_kategori' => 'required|string|max:20|unique:kategori,kode_kategori',
            'nama'          => 'required|string|max:100',
            'deskripsi'     => 'nullable|string',
        ]);

        Kategori::create($request->only('kode_kategori', 'nama', 'deskripsi'));

        return redirect()->route('kategori.index')->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function update(Request $request, Kategori $kategori)
    {
        $request->validate([
            'kode_kategori' => 'required|string|max:20|unique:kategori,kode_kategori,' . $kategori->id,
            'nama'          => 'required|string|max:100',
            'deskripsi'     => 'nullable|string',
        ]);

        $kategori->update($request->only('kode_kategori', 'nama', 'deskripsi'));

        return redirect()->route('kategori.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Kategori $kategori)
    {
        if ($kategori->barang()->exists()) {
            return redirect()->route('kategori.index')
                ->with('error', 'Kategori tidak dapat dihapus karena masih memiliki barang.');
        }

        $kategori->delete();

        return redirect()->route('kategori.index')->with('success', 'Kategori berhasil dihapus.');
    }
}
