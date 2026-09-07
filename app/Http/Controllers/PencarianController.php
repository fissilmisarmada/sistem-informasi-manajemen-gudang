<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Kategori;
use App\Models\MutasiBarang;
use Illuminate\Http\Request;

class PencarianController extends Controller
{
    public function lookupInput(Request $request)
    {
        $kodeBarang = trim((string) $request->input('kode_barang'));

        if ($kodeBarang === '') {
            return response()->json(['found' => false]);
        }

        $barang = Barang::with('kategori')->where('kode_barang', $kodeBarang)->first();

        if (!$barang) {
            return response()->json(['found' => false]);
        }

        return response()->json([
            'found' => true,
            'barang' => [
                'nama' => $barang->nama,
                'kategori_id' => $barang->kategori_id,
                'kategori' => $barang->kategori?->nama,
                'satuan' => $barang->satuan,
                'stok' => $barang->stok,
                'gambar' => $barang->gambar ? asset('storage/' . $barang->gambar) : null,
            ],
        ]);
    }

    public function input()
    {
        return view('pencarian.input', [
            'kategoris' => Kategori::orderBy('nama')->get(),
            'raks' => \App\Models\Rak::orderBy('kode_rak')->get(),
        ]);
    }

    public function prosesInput(Request $request)
    {
        $data = $request->validate([
            'kategori_id' => 'required|exists:kategori,id',
            'rak_id' => 'required|exists:rak,id',
            'kode_barang' => 'required|string|max:100',
            'jumlah' => 'required|integer|min:1',
        ]);

        $barang = Barang::where('kode_barang', $data['kode_barang'])->first();

        if ($barang) {
            MutasiBarang::catat($barang, $request->user(), 'masuk', $data['jumlah'], 'Input melalui scan barcode');

            $barang->update(['rak_id' => $data['rak_id']]);

            return back()->with('success', "{$barang->nama} berhasil ditambahkan {$data['jumlah']} {$barang->satuan}.");
        }

        return view('pencarian.input', [
            'kategoris' => Kategori::orderBy('nama')->get(),
            'raks' => \App\Models\Rak::orderBy('kode_rak')->get(),
            'manualData' => $data,
        ])->with('warning', 'Barcode belum dikenali. Lengkapi data barang secara manual.');
    }

    public function simpanInputManual(Request $request)
    {
        $data = $request->validate([
            'kategori_id' => 'required|exists:kategori,id',
            'rak_id' => 'required|exists:rak,id',
            'kode_barang' => 'required|string|max:100|unique:barang,kode_barang',
            'nama' => 'required|string|max:255',
            'gambar' => 'nullable|image|max:2048',
            'jumlah' => 'required|integer|min:1',
        ]);

        if ($request->hasFile('gambar')) {
            $data['gambar'] = $request->file('gambar')->store('barang', 'public');
        }

        $barang = Barang::create([
            'kategori_id' => $data['kategori_id'],
            'rak_id' => $data['rak_id'],
            'kode_barang' => $data['kode_barang'],
            'nama' => $data['nama'],
            'gambar' => $data['gambar'] ?? null,
            'satuan' => 'pcs',
            'stok' => 0,
            'stok_minimum' => 0,
        ]);

        MutasiBarang::catat($barang, $request->user(), 'masuk', $data['jumlah'], 'Input barang baru melalui scan barcode');

        return redirect()->route('pencarian.input')->with('success', "Barang {$barang->nama} berhasil ditambahkan.");
    }

    /**
     * Pencarian global: mencari di semua barang gudang (termasuk buku)
     * Buku disimpan sebagai Barang dengan kategori_id = 'BKU'
     */
    public function index(Request $request)
    {
        $q          = $request->input('q', '');
        $kategori_id = $request->input('kategori_id');
        $tipe       = $request->input('tipe', 'semua'); // semua | buku | barang

        // Ambil kategori Buku
        $bukuKategori = Kategori::where('kode_kategori', 'BKU')->first();
        $bukuKategoriId = $bukuKategori?->id;

        $query = Barang::with(['kategori', 'rak']);

        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('nama', 'like', "%{$q}%")
                    ->orWhere('kode_barang', 'like', "%{$q}%");
            });
        }

        // Filter berdasarkan tipe (buku/barang/semua)
        if ($tipe === 'buku' && $bukuKategoriId) {
            $query->where('kategori_id', $bukuKategoriId);
        } elseif ($tipe === 'barang' && $bukuKategoriId) {
            $query->where('kategori_id', '!=', $bukuKategoriId);
        }

        // Filter berdasarkan kategori
        if ($kategori_id) {
            $query->where('kategori_id', $kategori_id);
        }

        $barang = $query->orderBy('nama')->get();

        // Pisahkan buku dan barang lainnya untuk penghitungan di view
        $buku = $barang->filter(fn($item) => $item->kategori_id == $bukuKategoriId);
        $barangLain = $barang->filter(fn($item) => $item->kategori_id != $bukuKategoriId);

        $kategoris   = Kategori::orderBy('nama')->get();
        $totalHasil  = $barang->count();

        return view('pencarian.index', compact('buku', 'barangLain', 'barang', 'kategoris', 'q', 'tipe', 'kategori_id', 'totalHasil'));
    }
}

