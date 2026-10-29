<?php

namespace Tests\Unit;

use App\Support\Xlsx\XlsxReader;
use App\Support\Xlsx\XlsxWriter;
use PHPUnit\Framework\TestCase;
use ZipArchive;

class XlsxWriterTest extends TestCase
{
    public function test_workbook_ditulis_dan_dibaca_kembali(): void
    {
        $writer = new XlsxWriter('Tes');

        $writer->addSheet('Data', [
            [
                'columns' => [
                    ['label' => 'Kode', 'width' => 14],
                    ['label' => 'Nama', 'width' => 24],
                    ['label' => 'Stok', 'width' => 10, 'type' => 'number'],
                    ['label' => 'Status', 'width' => 14, 'type' => 'status'],
                ],
                'rows' => [
                    [
                        'A-1',
                        'Barang Satu',
                        ['value' => 5, 'type' => 'number'],
                        ['value' => 'Aman', 'type' => 'status', 'status' => 'ok'],
                    ],
                ],
            ],
        ], [
            'title' => 'DATA TES',
            'subtitle' => 'Subjudul',
            'freeze' => true,
            'autofilter' => true,
            'zebra' => true,
        ]);

        $writer->addSheet('Ringkasan', [
            [
                'heading' => 'RINGKASAN',
                'columns' => [
                    ['label' => 'Metrik', 'width' => 24],
                    ['label' => 'Nilai', 'width' => 12, 'type' => 'number'],
                ],
                'rows' => [
                    ['Total item', ['value' => 1, 'type' => 'number']],
                ],
            ],
        ], [
            'title' => 'RINGKASAN TES',
        ]);

        $path = tempnam(sys_get_temp_dir(), 'xlsx_test_');
        $writer->save($path);

        $zip = new ZipArchive;
        $this->assertTrue($zip->open($path) === true);

        $workbook = (string) $zip->getFromName('xl/workbook.xml');
        $sheet = (string) $zip->getFromName('xl/worksheets/sheet1.xml');
        $styles = (string) $zip->getFromName('xl/styles.xml');

        $this->assertStringContainsString('name="Data"', $workbook);
        $this->assertStringContainsString('name="Ringkasan"', $workbook);
        $this->assertStringContainsString('DATA TES', $sheet);
        $this->assertStringContainsString('<v>5</v>', $sheet);
        $this->assertStringContainsString('state="frozen"', $sheet);
        $this->assertStringContainsString('<autoFilter ref="A4:D5"/>', $sheet);
        $this->assertStringContainsString('formatCode="#,##0"', $styles);

        $zip->close();

        $rows = (new XlsxReader)->rows($path);

        $this->assertCount(5, $rows);
        $this->assertSame(['Kode', 'Nama', 'Stok', 'Status'], $rows[3]);
        $this->assertSame('A-1', $rows[4][0]);
        $this->assertSame('Barang Satu', $rows[4][1]);
        $this->assertSame('5', $rows[4][2]);
        $this->assertSame('Aman', $rows[4][3]);

        @unlink($path);
    }
}
