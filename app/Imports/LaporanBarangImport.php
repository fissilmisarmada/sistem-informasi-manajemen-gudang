<?php

namespace App\Imports;

use App\Models\Barang;
use App\Models\Kategori;
use App\Models\Rak;
use App\Support\Xlsx\XlsxReader;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;
use RuntimeException;

class LaporanBarangImport
{
    /**
     * @var array<string, string>
     */
    private const HEADER_ALIASES = [
        'kodebarang' => 'kode_barang',
        'kode' => 'kode_barang',
        'nama' => 'nama',
        'namabarang' => 'nama',
        'kodekategori' => 'kode_kategori',
        'kategorikode' => 'kode_kategori',
        'kategori' => 'kategori',
        'namakategori' => 'kategori',
        'satuan' => 'satuan',
        'stok' => 'stok',
        'stokminimum' => 'stok_minimum',
        'minimum' => 'stok_minimum',
        'minstok' => 'stok_minimum',
        'koderak' => 'kode_rak',
        'rak' => 'kode_rak',
        'lokasirak' => 'lokasi_rak',
        'status' => 'status',
        'isbn' => 'isbn',
        'kategoriasli' => 'kategori_asli',
        'jumlahhalaman' => 'jumlah_halaman',
        'halaman' => 'jumlah_halaman',
        'keterangan' => 'catatan',
        'catatan' => 'catatan',
    ];

    /**
     * @return array{imported: int, conflicts: int, invalid: int}
     */
    public function import(UploadedFile $file): array
    {
        try {
            $rows = $this->readRows($file);
        } catch (InvalidArgumentException|RuntimeException $exception) {
            throw ValidationException::withMessages([
                'file' => $exception->getMessage(),
            ]);
        }

        if ($rows === []) {
            throw ValidationException::withMessages([
                'file' => 'File tidak berisi data yang bisa diimpor.',
            ]);
        }

        [$headerIndex, $map] = $this->locateHeader($rows);

        if ($headerIndex === null) {
            throw ValidationException::withMessages([
                'file' => 'Format file tidak sesuai. Wajib ada kolom: Kode Barang, Nama Barang, dan Kode Kategori.',
            ]);
        }

        $imported = 0;
        $conflicts = 0;
        $invalidRows = 0;

        DB::transaction(function () use ($rows, $headerIndex, $map, &$imported, &$conflicts, &$invalidRows): void {
            foreach (array_slice($rows, $headerIndex + 1) as $row) {
                if ($this->isBlankRow($row)) {
                    continue;
                }

                $kodeBarang = $this->stringValue($row, $map, 'kode_barang');

                if ($kodeBarang === '') {
                    $invalidRows++;

                    continue;
                }

                if (Barang::where('kode_barang', $kodeBarang)->exists()) {
                    $conflicts++;

                    continue;
                }

                $kategori = $this->resolveKategori($row, $map);

                if ($kategori === null) {
                    $invalidRows++;

                    continue;
                }

                $rakKode = $this->stringValue($row, $map, 'kode_rak');
                $rak = $rakKode !== '' ? Rak::where('kode_rak', $rakKode)->first() : null;
                $satuan = $this->stringValue($row, $map, 'satuan');
                $keterangan = $this->keterangan($row, $map);

                Barang::create([
                    'kode_barang' => $kodeBarang,
                    'nama' => $this->stringValue($row, $map, 'nama'),
                    'kategori_id' => $kategori->id,
                    'satuan' => $satuan !== '' ? $satuan : 'pcs',
                    'stok' => max(0, $this->intValue($this->stringValue($row, $map, 'stok'))),
                    'stok_minimum' => Barang::stokMinimumForKategori($kategori->kode_kategori),
                    'rak_id' => $rak?->id,
                    'keterangan' => $keterangan !== [] ? $keterangan : null,
                ]);

                $imported++;
            }
        });

        return [
            'imported' => $imported,
            'conflicts' => $conflicts,
            'invalid' => $invalidRows,
        ];
    }

    /**
     * @return array<int, array<int, string|null>>
     */
    private function readRows(UploadedFile $file): array
    {
        $path = $file->getRealPath();

        if ($path === false || ! is_readable($path)) {
            throw new InvalidArgumentException('File tidak dapat dibaca.');
        }

        $handle = fopen($path, 'rb');

        if ($handle === false) {
            throw new InvalidArgumentException('File tidak dapat dibaca.');
        }

        $magic = (string) fread($handle, 4);
        fclose($handle);

        if ($magic === "PK\x03\x04") {
            return (new XlsxReader)->rows($path);
        }

        return $this->csvRows($path);
    }

    /**
     * @return array<int, array<int, string|null>>
     */
    private function csvRows(string $path): array
    {
        $handle = fopen($path, 'rb');

        if ($handle === false) {
            throw new InvalidArgumentException('File CSV tidak dapat dibaca.');
        }

        $firstLine = (string) fgets($handle);
        rewind($handle);
        $delimiter = $this->detectDelimiter($firstLine);

        $rows = [];
        $firstRow = true;

        while (($row = fgetcsv($handle, null, $delimiter)) !== false) {
            if ($firstRow) {
                if (isset($row[0]) && is_string($row[0])) {
                    $row[0] = preg_replace('/^\xEF\xBB\xBF/', '', $row[0]) ?? $row[0];
                }

                $firstRow = false;
            }

            $rows[] = array_map(
                fn ($value) => $value === null ? null : (string) $value,
                $row
            );
        }

        fclose($handle);

        return $rows;
    }

    private function detectDelimiter(string $line): string
    {
        $candidates = [
            ',' => substr_count($line, ','),
            ';' => substr_count($line, ';'),
            "\t" => substr_count($line, "\t"),
        ];

        arsort($candidates);
        $delimiter = (string) array_key_first($candidates);

        return ($candidates[$delimiter] ?? 0) > 0 ? $delimiter : ',';
    }

    /**
     * @param  array<int, array<int, string|null>>  $rows
     * @return array{0: int|null, 1: array<string, int>}
     */
    private function locateHeader(array $rows): array
    {
        $bestIndex = null;
        $bestMap = [];
        $bestScore = 0;

        foreach (array_slice($rows, 0, 15, true) as $index => $row) {
            $map = [];

            foreach ($row as $columnIndex => $value) {
                $key = $this->headerKey($value);

                if ($key === null || isset($map[$key])) {
                    continue;
                }

                $map[$key] = $columnIndex;
            }

            $hasKode = isset($map['kode_barang']);
            $hasNama = isset($map['nama']);
            $hasKategori = isset($map['kode_kategori']) || isset($map['kategori']);

            if (! $hasKode || ! $hasNama || ! $hasKategori) {
                continue;
            }

            $score = count($map);

            if ($score > $bestScore) {
                $bestIndex = $index;
                $bestMap = $map;
                $bestScore = $score;
            }
        }

        return [$bestIndex, $bestMap];
    }

    private function headerKey(mixed $value): ?string
    {
        $normalized = preg_replace('/[^a-z0-9]+/', '', strtolower(trim((string) $value))) ?? '';

        return self::HEADER_ALIASES[$normalized] ?? null;
    }

    /**
     * @param  array<int, string|null>  $row
     * @param  array<string, int>  $map
     */
    private function resolveKategori(array $row, array $map): ?Kategori
    {
        $kode = $this->stringValue($row, $map, 'kode_kategori');

        if ($kode !== '') {
            return Kategori::where('kode_kategori', $kode)->first();
        }

        $nama = $this->stringValue($row, $map, 'kategori');

        if ($nama !== '') {
            return Kategori::whereRaw('LOWER(nama) = ?', [mb_strtolower($nama)])->first();
        }

        return null;
    }

    /**
     * @param  array<int, string|null>  $row
     * @param  array<string, int>  $map
     */
    private function isBlankRow(array $row): bool
    {
        foreach ($row as $value) {
            if ($value !== null && trim((string) $value) !== '') {
                return false;
            }
        }

        return true;
    }

    /**
     * @param  array<int, string|null>  $row
     * @param  array<string, int>  $map
     */
    private function stringValue(array $row, array $map, string $key): string
    {
        $index = $map[$key] ?? null;

        if ($index === null) {
            return '';
        }

        $value = $row[$index] ?? '';

        if ($value === null) {
            return '';
        }

        if (is_float($value)) {
            return rtrim(rtrim(number_format($value, 6, '.', ''), '0'), '.');
        }

        return trim((string) $value);
    }

    /**
     * @param  array<int, string|null>  $row
     * @param  array<string, int>  $map
     * @return array<string, mixed>
     */
    private function keterangan(array $row, array $map): array
    {
        $catatan = $this->stringValue($row, $map, 'catatan');
        $decoded = [];

        if ($catatan !== '' && str_starts_with($catatan, '{')) {
            $json = json_decode($catatan, true);

            if (is_array($json)) {
                $decoded = $json;
                $catatan = isset($json['catatan']) ? (string) $json['catatan'] : '';
            }
        }

        $jumlahHalaman = $this->intValueOrNull($this->stringValue($row, $map, 'jumlah_halaman'));

        if ($jumlahHalaman === null && isset($decoded['jumlah_halaman'])) {
            $jumlahHalaman = (int) $decoded['jumlah_halaman'];
        }

        return array_filter([
            'isbn' => $this->stringValue($row, $map, 'isbn') ?: ($decoded['isbn'] ?? ''),
            'kategori_asli' => $this->stringValue($row, $map, 'kategori_asli') ?: ($decoded['kategori_asli'] ?? ''),
            'jumlah_halaman' => $jumlahHalaman,
            'catatan' => $catatan,
        ], fn ($value) => $value !== '' && $value !== null);
    }

    private function parseNumber(string $value): ?float
    {
        $value = trim($value);

        if ($value === '') {
            return null;
        }

        if (str_contains($value, ',')) {
            $value = str_replace('.', '', $value);
            $value = str_replace(',', '.', $value);
        } elseif (preg_match('/^\d{1,3}(\.\d{3})+$/', $value) === 1) {
            $value = str_replace('.', '', $value);
        }

        $value = str_replace(' ', '', $value);

        return is_numeric($value) ? (float) $value : null;
    }

    private function intValue(string $value): int
    {
        $number = $this->parseNumber($value);

        return $number === null ? 0 : (int) floor($number);
    }

    private function intValueOrNull(string $value): ?int
    {
        $number = $this->parseNumber($value);

        return $number === null ? null : (int) floor($number);
    }
}
