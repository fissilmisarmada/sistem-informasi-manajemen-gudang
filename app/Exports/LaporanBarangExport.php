<?php

namespace App\Exports;

use App\Models\Barang;
use App\Support\Xlsx\XlsxWriter;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class LaporanBarangExport
{
    /**
     * @var array<int, array{label: string, width: int, type?: string}>
     */
    private const COLUMNS = [
        ['label' => 'Kode Barang', 'width' => 16],
        ['label' => 'Nama Barang', 'width' => 34],
        ['label' => 'Kode Kategori', 'width' => 14],
        ['label' => 'Kategori', 'width' => 20],
        ['label' => 'Satuan', 'width' => 10],
        ['label' => 'Stok', 'width' => 10, 'type' => 'number'],
        ['label' => 'Stok Minimum', 'width' => 13, 'type' => 'number'],
        ['label' => 'Kode Rak', 'width' => 14],
        ['label' => 'Lokasi Rak', 'width' => 22],
        ['label' => 'Status', 'width' => 14, 'type' => 'status'],
        ['label' => 'ISBN', 'width' => 18],
        ['label' => 'Kategori Asli', 'width' => 20],
        ['label' => 'Jumlah Halaman', 'width' => 15, 'type' => 'number'],
        ['label' => 'Catatan', 'width' => 40, 'type' => 'wrap'],
    ];

    public function __construct(
        private Collection $barangs,
        private array $ringkasan
    ) {}

    public function download(): BinaryFileResponse
    {
        $writer = new XlsxWriter;

        $writer->addSheet('Laporan Barang', [
            [
                'columns' => self::COLUMNS,
                'rows' => $this->barangRows(),
            ],
        ], [
            'title' => 'LAPORAN GUDANG',
            'subtitle' => $this->subtitle(),
            'freeze' => true,
            'autofilter' => true,
            'zebra' => true,
            'landscape' => true,
        ]);

        $writer->addSheet('Ringkasan', [
            [
                'heading' => 'RINGKASAN INVENTARIS',
                'columns' => [
                    ['label' => 'Metrik', 'width' => 30],
                    ['label' => 'Nilai', 'width' => 16, 'type' => 'number'],
                    ['label' => 'Catatan', 'width' => 42],
                ],
                'rows' => $this->ringkasanRows(),
            ],
            [
                'heading' => 'BARANG PER KATEGORI',
                'columns' => [
                    ['label' => 'Kategori', 'width' => 30],
                    ['label' => 'Jumlah Barang', 'width' => 16, 'type' => 'number'],
                    ['label' => 'Total Stok', 'width' => 16, 'type' => 'number'],
                ],
                'rows' => $this->kategoriRows(),
            ],
        ], [
            'title' => 'RINGKASAN GUDANG',
            'subtitle' => $this->subtitle(),
            'freeze' => false,
            'autofilter' => false,
            'zebra' => false,
            'landscape' => false,
        ]);

        return $writer->download('laporan-gudang-'.now()->format('Y-m-d-His').'.xlsx');
    }

    /**
     * @return array<int, array<int, mixed>>
     */
    private function barangRows(): array
    {
        return $this->barangs->map(function (Barang $barang): array {
            $keterangan = $this->keterangan($barang);
            $menipis = $barang->isStokMenipis();

            return [
                $barang->kode_barang,
                $barang->nama,
                $barang->kategori?->kode_kategori ?? '-',
                $barang->kategori?->nama ?? '-',
                $barang->satuan,
                ['value' => (int) $barang->stok, 'type' => 'number'],
                ['value' => (int) $barang->stok_minimum, 'type' => 'number'],
                $barang->rak?->kode_rak ?? '-',
                $barang->rak?->nama_lokasi ?? '-',
                [
                    'value' => $menipis ? 'Stok menipis' : 'Aman',
                    'type' => 'status',
                    'status' => $menipis ? 'low' : 'ok',
                ],
                $keterangan['isbn'] ?? '',
                $keterangan['kategori_asli'] ?? '',
                ['value' => $keterangan['jumlah_halaman'] ?? '', 'type' => 'number'],
                ['value' => $keterangan['catatan'] ?? '', 'type' => 'wrap'],
            ];
        })->all();
    }

    /**
     * @return array<int, array<int, mixed>>
     */
    private function ringkasanRows(): array
    {
        return [
            ['Total item', ['value' => (int) ($this->ringkasan['total_item'] ?? 0), 'type' => 'number'], 'Barang terdaftar'],
            ['Total stok', ['value' => (int) ($this->ringkasan['total_stok'] ?? 0), 'type' => 'number'], 'Jumlah seluruh satuan'],
            ['Stok menipis', ['value' => (int) ($this->ringkasan['stok_menipis'] ?? 0), 'type' => 'number'], 'Perlu perhatian'],
            ['Ditempatkan', ['value' => (int) ($this->ringkasan['ditempatkan'] ?? 0), 'type' => 'number'], 'Memiliki lokasi rak'],
            ['Kategori', ['value' => (int) ($this->ringkasan['kategori'] ?? 0), 'type' => 'number'], 'Kategori terdaftar'],
            ['Rak', ['value' => (int) ($this->ringkasan['rak'] ?? 0), 'type' => 'number'], 'Rak terdaftar'],
            ['Riwayat mutasi', ['value' => (int) ($this->ringkasan['mutasi'] ?? 0), 'type' => 'number'], 'Transaksi masuk/keluar'],
            ['Stock opname', ['value' => (int) ($this->ringkasan['opname'] ?? 0), 'type' => 'number'], 'Pemeriksaan fisik'],
            ['Waktu ekspor', ['value' => now()->format('d/m/Y H:i'), 'type' => 'text'], ''],
        ];
    }

    /**
     * @return array<int, array<int, mixed>>
     */
    private function kategoriRows(): array
    {
        return $this->barangs
            ->groupBy(fn (Barang $barang) => $barang->kategori?->nama ?? 'Tanpa kategori')
            ->sortKeys()
            ->map(fn (Collection $items, string $nama) => [
                $nama,
                ['value' => $items->count(), 'type' => 'number'],
                ['value' => (int) $items->sum('stok'), 'type' => 'number'],
            ])
            ->values()
            ->all();
    }

    private function subtitle(): string
    {
        return 'Sistem Informasi Manajemen Gudang · Diekspor '.now()->format('d/m/Y H:i')
            .' · '.number_format($this->barangs->count(), 0, ',', '.').' item';
    }

    /**
     * @return array<string, mixed>
     */
    private function keterangan(Barang $barang): array
    {
        $raw = $barang->keterangan;

        if (is_string($raw)) {
            $decoded = json_decode($raw, true);
            $raw = is_array($decoded) ? $decoded : ['catatan' => $raw];
        }

        if (! is_array($raw)) {
            return [];
        }

        if (! isset($raw['catatan']) && isset($raw['keterangan'])) {
            $raw['catatan'] = $raw['keterangan'];
        }

        return $raw;
    }
}
