<?php

namespace App\Http\Controllers;

use App\Exports\LaporanBarangExport;
use App\Imports\LaporanBarangImport;
use App\Models\Barang;
use App\Models\Kategori;
use App\Models\MutasiBarang;
use App\Models\Rak;
use App\Models\StockOpnameBarang;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class LaporanController extends Controller
{
    public function index()
    {
        $barangs = Barang::with(['kategori', 'rak'])->orderBy('nama')->get();
        $mutasiTerbaru = MutasiBarang::with(['barang', 'staff'])->latest('created_at')->take(8)->get();

        return view('laporan.index', [
            'barangs' => $barangs,
            'mutasiTerbaru' => $mutasiTerbaru,
            'ringkasan' => $this->ringkasan($barangs),
        ]);
    }

    public function export(): BinaryFileResponse
    {
        $barangs = Barang::with(['kategori', 'rak'])->orderBy('nama')->get();

        return (new LaporanBarangExport($barangs, $this->ringkasan($barangs)))->download();
    }

    public function exportCsv(): Response
    {
        $fileName = 'laporan'.now()->format('Y-m-d-His').'.csv';
        $barangs = Barang::with(['kategori', 'rak'])->orderBy('nama')->get();

        $handle = fopen('php://temp', 'r+');
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
                is_array($barang->keterangan) ? json_encode($barang->keterangan) : $barang->keterangan,
            ]);
        }

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$fileName.'"',
        ]);
    }

    public function import(Request $request, LaporanBarangImport $importer): RedirectResponse
    {
        $request->validate([
            'file' => 'required|file|max:5120|extensions:csv,txt,xlsx',
        ]);

        $result = $importer->import($request->file('file'));

        $msg = "Import selesai. {$result['imported']} data barang baru ditambahkan.";

        if ($result['conflicts'] > 0 || $result['invalid'] > 0) {
            $details = [];

            if ($result['conflicts'] > 0) {
                $details[] = "{$result['conflicts']} data ditolak karena konflik (kode_barang sudah ada)";
            }

            if ($result['invalid'] > 0) {
                $details[] = "{$result['invalid']} baris ditolak (kategori/format tidak valid)";
            }

            $msg .= ' ('.implode(', ', $details).').';
        }

        return redirect()->route('laporan.index')->with('sukses', $msg);
    }

    /**
     * @return array<string, int>
     */
    private function ringkasan(Collection $barangs): array
    {
        return [
            'total_item' => $barangs->count(),
            'total_stok' => (int) $barangs->sum('stok'),
            'stok_menipis' => $barangs->filter(fn (Barang $barang) => $barang->isStokMenipis())->count(),
            'ditempatkan' => $barangs->whereNotNull('rak_id')->count(),
            'kategori' => Kategori::count(),
            'rak' => Rak::count(),
            'mutasi' => MutasiBarang::count(),
            'opname' => StockOpnameBarang::count(),
        ];
    }
}
