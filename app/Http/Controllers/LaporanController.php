<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Rak;
use App\Models\RiwayatPenempatan;
use App\Models\StockOpname;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    public function index()
    {
        $bukus = Buku::with('rak')->orderBy('judul')->get();
        $ringkasan = [
            'buku' => $bukus->count(),
            'buku_ditempatkan' => $bukus->whereNotNull('rak_id')->count(),
            'rak' => Rak::count(),
            'penempatan' => RiwayatPenempatan::count(),
            'opname' => StockOpname::count(),
        ];

        return view('laporan.index', compact('bukus', 'ringkasan'));
    }

    public function export()
    {
        $fileName = 'laporan' . now()->format('Y-m-d-His') . '.csv';
        $bukus = Buku::with('rak')->orderBy('judul')->cursor();

        return response()->streamDownload(function () use ($bukus) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['kode_buku', 'judul', 'isbn', 'eisbn', 'jumlah_halaman', 'kode_rak', 'nama_lokasi']);

            foreach ($bukus as $buku) {
                fputcsv($handle, [
                    $buku->kode_buku,
                    $buku->judul,
                    $buku->isbn,
                    $buku->eisbn,
                    $buku->jumlah_halaman,
                    $buku->rak?->kode_rak,
                    $buku->rak?->nama_lokasi,
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
        $expectedHeaders = ['kode_buku', 'judul', 'isbn', 'eisbn', 'jumlah_halaman', 'kode_rak', 'nama_lokasi'];

        if ($headers !== $expectedHeaders) {
            fclose($handle);

            return back()->withErrors([
                'file' => 'Format CSV tidak sesuai. Gunakan file hasil export laporan.',
            ]);
        }

        $imported = 0;
        DB::transaction(function () use ($handle, &$imported) {
            while (($row = fgetcsv($handle)) !== false) {
                if (count($row) < 7 || trim((string) $row[0]) === '') {
                    continue;
                }

                $rak = trim((string) $row[5]) !== ''
                    ? Rak::where('kode_rak', trim($row[5]))->first()
                    : null;

                Buku::updateOrCreate(
                    ['kode_buku' => trim($row[0])],
                    [
                        'judul' => trim($row[1]),
                        'isbn' => trim($row[2]) !== '' ? trim($row[2]) : null,
                        'eisbn' => trim($row[3]) !== '' ? trim($row[3]) : null,
                        'jumlah_halaman' => is_numeric($row[4]) ? (int) $row[4] : null,
                        'rak_id' => $rak?->id,
                    ]
                );
                $imported++;
            }
        });

        fclose($handle);

        return redirect()->route('laporan.index')->with('sukses', "Import selesai. {$imported} data buku diproses.");
    }
}