<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Kategori;
use App\Models\MutasiBarang;
use App\Models\Rak;
use App\Models\StockOpnameBarang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    public function index()
    {
        $barangs = Barang::with(['kategori', 'rak'])->orderBy('nama')->get();
        $mutasiTerbaru = MutasiBarang::with(['barang', 'staff'])->latest('created_at')->take(8)->get();
        $ringkasan = [
            'total_item' => $barangs->count(),
            'total_stok' => $barangs->sum('stok'),
            'stok_menipis' => $barangs->filter->isStokMenipis()->count(),
            'ditempatkan' => $barangs->whereNotNull('rak_id')->count(),
            'kategori' => Kategori::count(),
            'rak' => Rak::count(),
            'mutasi' => MutasiBarang::count(),
            'opname' => StockOpnameBarang::count(),
        ];

        return view('laporan.index', compact('barangs', 'mutasiTerbaru', 'ringkasan'));
    }

    public function export()
    {
        $fileName = 'laporan' . now()->format('Y-m-d-His') . '.csv';
        $barangs = Barang::with(['kategori', 'rak'])->orderBy('nama')->cursor();

        return response()->streamDownload(function () use ($barangs) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['kode_barang', 'nama', 'kode_kategori', 'kategori', 'satuan', 'stok', 'stok_minimum', 'kode_rak', 'keterangan']);

            foreach ($barangs as $barang) {
                fputcsv($handle, [
                    $barang->kode_barang,
                    $barang->nama,
                    $barang->kategori?->kode_kategori,
                    $barang->kategori?->nama,
                    $barang->satuan,
                    $barang->stok,
                    $barang->stok_minimum,
                    $barang->rak?->kode_rak,
                    $barang->keterangan,
                ]);
            }

            fclose($handle);
        }, $fileName, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:5120',
        ]);

        $handle = fopen($request->file('file')->getRealPath(), 'r');
        $headers = fgetcsv($handle);
        $expectedHeaders = ['kode_barang', 'nama', 'kode_kategori', 'kategori', 'satuan', 'stok', 'stok_minimum', 'kode_rak', 'keterangan'];

        if ($headers !== $expectedHeaders) {
            fclose($handle);

            return back()->withErrors([
                'file' => 'Format CSV tidak sesuai. Gunakan file hasil export laporan.',
            ]);
        }

        $imported = 0;
        DB::transaction(function () use ($handle, &$imported) {
            while (($row = fgetcsv($handle)) !== false) {
                if (count($row) < 9 || trim((string) $row[0]) === '') {
                    continue;
                }

                $rak = trim((string) $row[7]) !== ''
                    ? Rak::where('kode_rak', trim($row[7]))->first()
                    : null;
                $kategori = Kategori::where('kode_kategori', trim($row[2]))->first();

                if (!$kategori) {
                    continue;
                }

                Barang::updateOrCreate(
                    ['kode_barang' => trim($row[0])],
                    [
                        'nama' => trim($row[1]),
                        'kategori_id' => $kategori->id,
                        'satuan' => trim($row[4]) !== '' ? trim($row[4]) : 'pcs',
                        'stok' => is_numeric($row[5]) ? max(0, (int) $row[5]) : 0,
                        'stok_minimum' => is_numeric($row[6]) ? max(0, (int) $row[6]) : 0,
                        'rak_id' => $rak?->id,
                        'keterangan' => trim($row[8]) !== '' ? trim($row[8]) : null,
                    ]
                );
                $imported++;
            }
        });

        fclose($handle);

        return redirect()->route('laporan.index')->with('sukses', "Import selesai. {$imported} data barang diproses.");
    }
}
