<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Rak;
use App\Models\RiwayatPenempatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BukuController extends Controller
{
    /**
     * Menampilkan daftar semua buku.
     */
    public function index(Request $request)
    {
        $query = Buku::with('rak')->orderBy('judul');

        if ($request->filled('q')) {
            $search = $request->string('q')->toString();
            $query->where(function ($builder) use ($search) {
                $builder->where('judul', 'like', "%{$search}%")
                    ->orWhere('kode_buku', 'like', "%{$search}%")
                    ->orWhere('isbn', 'like', "%{$search}%");
            });
        }

        $bukus = $query->paginate(15)->withQueryString();
        return view('buku.index', compact('bukus'));
    }

    public function cariBuku(Request $request)
    {
        $query = Buku::with('rak')->orderBy('judul');

        if ($request->filled('q')) {
            $search = $request->string('q')->toString();
            $query->where(function ($builder) use ($search) {
                $builder->where('judul', 'like', "%{$search}%")
                    ->orWhere('kode_buku', 'like', "%{$search}%")
                    ->orWhere('isbn', 'like', "%{$search}%")
                    ->orWhereHas('rak', function ($rakQuery) use ($search) {
                        $rakQuery->where('kode_rak', 'like', "%{$search}%")
                            ->orWhere('nama_lokasi', 'like', "%{$search}%");
                    });
            });
        }

        $bukus = $query->paginate(10)->withQueryString();

        return view('buku.cari', compact('bukus'));
    }

    public function hasilCariBuku(Request $request)
    {
        $request->validate(['q' => 'required|string|max:100']);
        $search = $request->string('q')->toString();

        $buku = Buku::with('rak')
            ->where('judul', 'like', "%{$search}%")
            ->orWhere('kode_buku', 'like', "%{$search}%")
            ->orWhere('isbn', 'like', "%{$search}%")
            ->first();

        return response()->json([
            'ditemukan' => (bool) $buku,
            'buku' => $buku,
        ]);
    }

    /**
     * Menampilkan form tambah data buku umum.
     */
    public function createData()
    {
        $dataRak = Rak::orderBy('kode_rak')->get();

        return view('buku.create', compact('dataRak'));
    }

    /**
     * Menyimpan data buku baru ke database.
     */
    public function storeData(Request $request)
    {
        $validated = $request->validate([
            'kode_buku'      => 'required|string|max:100|unique:buku,kode_buku',
            'judul'          => 'required|string|max:255',
            'cover'          => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'kategori'       => 'nullable|string|max:100',
            'isbn'           => 'required|string|max:100|unique:buku,isbn',
            'eisbn'          => 'nullable|string|max:100',
            'jumlah_halaman' => 'nullable|integer|min:1',
            'stok'           => 'required|integer|min:0',
            'rak_id'         => 'nullable|exists:rak,id',
        ]);

        if ($request->hasFile('cover')) {
            $validated['cover'] = $request->file('cover')->store('covers', 'public');
        }

        $buku = Buku::create($validated);

        if ($buku->rak_id) {
            RiwayatPenempatan::catat($buku, $buku->rak, Auth::user());
        }

        return redirect()->route('buku.index')->with('sukses', 'Data buku berhasil ditambahkan.');
    }

    /**
     * Menampilkan form edit data buku.
     */
    public function editData(Buku $buku)
    {
        return view('buku.edit', compact('buku'));
    }

    /**
     * Memperbarui data buku.
     */
    public function updateData(Request $request, Buku $buku)
    {
        $validated = $request->validate([
            'kode_buku'      => 'required|string|max:100|unique:buku,kode_buku,' . $buku->id,
            'judul'          => 'required|string|max:255',
            'cover'          => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'kategori'       => 'nullable|string|max:100',
            'isbn'           => 'required|string|max:100|unique:buku,isbn,' . $buku->id,
            'eisbn'          => 'nullable|string|max:100',
            'jumlah_halaman' => 'nullable|integer|min:1',
            'stok'           => 'required|integer|min:0',
        ]);

        if ($request->hasFile('cover')) {
            if ($buku->cover) {
                Storage::disk('public')->delete($buku->cover);
            }

            $validated['cover'] = $request->file('cover')->store('covers', 'public');
        }

        $buku->update($validated);

        return redirect()->route('buku.index')->with('sukses', 'Data buku berhasil diperbarui.');
    }

    /**
     * Menghapus data buku.
     */
    public function destroyData(Buku $buku)
    {
        if ($buku->cover) {
            Storage::disk('public')->delete($buku->cover);
        }

        $buku->delete();

        return redirect()->route('buku.index')->with('sukses', 'Data buku berhasil dihapus.');
    }

    /**
     * TAHAP 1 (sequence diagram pesan 1-10):
     * Menampilkan form "Tempatkan Buku ke Rak".
     * Mengambil semua data buku & rak buat ditampilkan di form.
     */
    public function create()
    {
        $dataBuku = Buku::with('rak')->orderBy('judul')->get();
        $dataRak  = Rak::with('buku')->orderBy('kode_rak')->get();

        return view('buku.tempatkan', [
            'dataBuku' => $dataBuku,
            'dataRak'  => $dataRak,
        ]);
    }

    /**
     * TAHAP 2 (sequence diagram pesan 11-18):
     * Dipanggil lewat AJAX begitu Staff selesai scan barcode.
     * Sistem cari apakah ISBN hasil scan itu udah terdaftar atau belum.
     *
     * Response JSON:
     * - kalau ketemu  -> { "ditemukan": true, "buku": {...} }
     * - kalau enggak  -> { "ditemukan": false, "isbn": "..." }
     */
    public function cariByIsbn(Request $request)
    {
        $request->validate([
            'isbn' => 'required|string',
        ]);

        $buku = Buku::findByIsbn($request->isbn);

        if ($buku) {
            return response()->json([
                'ditemukan' => true,
                'buku' => $buku,
            ]);
        }

        return response()->json([
            'ditemukan' => false,
            'isbn' => $request->isbn,
        ]);
    }

    /**
     * TAHAP 2b (sequence diagram pesan 19b-23b):
     * Dipanggil kalau ISBN belum terdaftar. Staff melengkapi data buku baru
     * (ISBN sudah otomatis terisi dari hasil scan), lalu disimpan.
     */
    public function simpanBukuBaru(Request $request)
    {
        $validated = $request->validate([
            'kode_buku'      => 'required|string|max:100|unique:buku,kode_buku',
            'judul'          => 'required|string|max:255',
            'isbn'           => 'required|string|max:100|unique:buku,isbn',
            'eisbn'          => 'nullable|string|max:100',
            'jumlah_halaman' => 'nullable|integer|min:1',
        ]);

        $buku = Buku::create($validated);

        // Balikin data buku yang baru dibuat, dipakai buat lanjut ke tahap pilih rak
        return response()->json([
            'sukses' => true,
            'buku'   => $buku,
        ]);
    }

    /**
     * TAHAP 3 (sequence diagram pesan 24-31):
     * Staff sudah pilih rak tujuan -> proses penempatan buku ke rak.
     *
     * Ada validasi penting di sini: buku yang SUDAH punya rak_id (sudah pernah
     * ditempatkan sebelumnya) akan DITOLAK, karena sistem ini tidak punya
     * fitur pindah rak (sudah dihapus dari ruang lingkup).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'buku_id' => 'required|exists:buku,id',
            'rak_id'  => 'required|exists:rak,id',
            'jumlah_masuk' => 'required|integer|min:1',
        ]);

        $buku = Buku::with('rak')->findOrFail($validated['buku_id']);
        $rak  = Rak::with('buku')->findOrFail($validated['rak_id']);
        $staff = Auth::user();
        $jumlahMasuk = (int) $validated['jumlah_masuk'];

        if ($buku->rak_id && $buku->rak_id !== $rak->id) {
            return back()->withErrors([
                'buku_id' => 'Buku ini sudah berada di rak lain. Gunakan rak yang sama untuk menambah stok.',
            ])->withInput();
        }

        $stokRak = (int) $rak->buku->sum('stok');
        if ($rak->kapasitas !== null && $stokRak + $jumlahMasuk > $rak->kapasitas) {
            return back()->withErrors([
                'rak_id' => "Kapasitas rak {$rak->kode_rak} tidak cukup. Sisa kapasitas: " . max(0, $rak->kapasitas - $stokRak) . ' buku.',
            ])->withInput();
        }

        DB::transaction(function () use ($buku, $rak, $staff, $jumlahMasuk) {
            $buku->rak_id = $rak->id;
            $buku->stok = (int) $buku->stok + $jumlahMasuk;
            $buku->save();
            RiwayatPenempatan::catat($buku, $rak, $staff);
        });

        return redirect()
            ->route('buku.create')
            ->with('sukses', "{$jumlahMasuk} buku \"{$buku->judul}\" berhasil ditempatkan ke rak {$rak->kode_rak}.");
    }

    public function show(Buku $buku)
    {
        $buku->load('rak');

        return view('buku.show', compact('buku'));
    }
}
