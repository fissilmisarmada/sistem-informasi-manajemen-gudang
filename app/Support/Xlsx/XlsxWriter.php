<?php

namespace App\Support\Xlsx;

use RuntimeException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use ZipArchive;

/**
 * Penulis XLSX ringan tanpa dependency eksternal.
 *
 * Hanya memakai ZipArchive + XML OOXML sehingga tidak menambah paket Composer.
 */
class XlsxWriter
{
    private const STYLE_TITLE = 1;

    private const STYLE_SUBTITLE = 2;

    private const STYLE_HEADER = 3;

    private const STYLE_TEXT = 4;

    private const STYLE_NUMBER = 5;

    private const STYLE_TEXT_ZEBRA = 6;

    private const STYLE_NUMBER_ZEBRA = 7;

    private const STYLE_STATUS_OK = 8;

    private const STYLE_STATUS_LOW = 9;

    private const STYLE_WRAP = 10;

    private const STYLE_WRAP_ZEBRA = 11;

    private const STYLE_SECTION = 12;

    /**
     * @var array<int, array{name: string, blocks: array, options: array}>
     */
    private array $sheets = [];

    public function __construct(private string $creator = 'SIM Gudang') {}

    /**
     * @param  array<int, array{heading?: string, columns: array<int, array{label: string, width?: float|int, type?: string}>, rows: array<int, array<int, mixed>>}>  $blocks
     * @param  array{title?: string, subtitle?: string, freeze?: bool, autofilter?: bool, zebra?: bool, landscape?: bool}  $options
     */
    public function addSheet(string $name, array $blocks, array $options = []): self
    {
        $this->sheets[] = [
            'name' => $this->sanitizeSheetName($name),
            'blocks' => $blocks,
            'options' => $options,
        ];

        return $this;
    }

    public function save(string $path): string
    {
        if ($this->sheets === []) {
            throw new RuntimeException('Workbook tidak memiliki sheet.');
        }

        $directory = dirname($path);

        if (! is_dir($directory) && ! mkdir($directory, 0775, true) && ! is_dir($directory)) {
            throw new RuntimeException('Tidak dapat membuat direktori workbook.');
        }

        if (file_exists($path)) {
            @unlink($path);
        }

        $zip = new ZipArchive;

        if ($zip->open($path, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new RuntimeException('Tidak dapat membuat file Excel.');
        }

        $totalSheets = count($this->sheets);

        $zip->addFromString('[Content_Types].xml', $this->contentTypesXml($totalSheets));
        $zip->addFromString('_rels/.rels', $this->rootRelsXml());
        $zip->addFromString('docProps/core.xml', $this->coreXml());
        $zip->addFromString('docProps/app.xml', $this->appXml());
        $zip->addFromString('xl/workbook.xml', $this->workbookXml());
        $zip->addFromString('xl/_rels/workbook.xml.rels', $this->workbookRelsXml($totalSheets));
        $zip->addFromString('xl/styles.xml', $this->stylesXml());

        foreach ($this->sheets as $index => $sheet) {
            $zip->addFromString(
                'xl/worksheets/sheet'.($index + 1).'.xml',
                $this->sheetXml($sheet, $index)
            );
        }

        $zip->close();

        return $path;
    }

    public function download(string $filename): BinaryFileResponse
    {
        $temp = tempnam(sys_get_temp_dir(), 'xlsx_');

        if ($temp === false) {
            throw new RuntimeException('Tidak dapat membuat file sementara.');
        }

        $this->save($temp);

        return response()
            ->download($temp, $filename, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ])
            ->deleteFileAfterSend(true);
    }

    private function contentTypesXml(int $sheetCount): string
    {
        $overrides = '';

        for ($index = 1; $index <= $sheetCount; $index++) {
            $overrides .= '<Override PartName="/xl/worksheets/sheet'.$index.'.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>';
        }

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            .'<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            .'<Default Extension="xml" ContentType="application/xml"/>'
            .'<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
            .'<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'
            .$overrides
            .'<Override PartName="/docProps/core.xml" ContentType="application/vnd.openxmlformats-package.core-properties+xml"/>'
            .'<Override PartName="/docProps/app.xml" ContentType="application/vnd.openxmlformats-officedocument.extended-properties+xml"/>'
            .'</Types>';
    }

    private function rootRelsXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            .'<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
            .'<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/package/2006/relationships/metadata/core-properties" Target="docProps/core.xml"/>'
            .'<Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/extended-properties" Target="docProps/app.xml"/>'
            .'</Relationships>';
    }

    private function coreXml(): string
    {
        $now = gmdate('Y-m-d\TH:i:s\Z');

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<cp:coreProperties xmlns:cp="http://schemas.openxmlformats.org/package/2006/metadata/core-properties" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:dcterms="http://purl.org/dc/terms/" xmlns:dcmitype="http://purl.org/dc/dcmitype/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">'
            .'<dc:creator>'.$this->esc($this->creator).'</dc:creator>'
            .'<cp:lastModifiedBy>'.$this->esc($this->creator).'</cp:lastModifiedBy>'
            .'<dcterms:created xsi:type="dcterms:W3CDTF">'.$now.'</dcterms:created>'
            .'<dcterms:modified xsi:type="dcterms:W3CDTF">'.$now.'</dcterms:modified>'
            .'</cp:coreProperties>';
    }

    private function appXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Properties xmlns="http://schemas.openxmlformats.org/officeDocument/2006/extended-properties" xmlns:vt="http://schemas.openxmlformats.org/officeDocument/2006/docPropsVTypes">'
            .'<Application>SIM Gudang</Application>'
            .'<DocSecurity>0</DocSecurity>'
            .'<ScaleCrop>false</ScaleCrop>'
            .'</Properties>';
    }

    private function workbookXml(): string
    {
        $sheets = '';
        $definedNames = '';

        foreach ($this->sheets as $index => $sheet) {
            $sheetId = $index + 1;
            $sheets .= '<sheet name="'.$this->esc($sheet['name']).'" sheetId="'.$sheetId.'" r:id="rId'.$sheetId.'"/>';

            $headerRow = $this->firstHeaderRow($sheet);

            if ($headerRow > 0) {
                $definedNames .= '<definedName name="_xlnm.Print_Titles" localSheetId="'.$index.'">'
                    .$this->esc($this->quotedSheetName($sheet['name']).'!$1:$'.$headerRow)
                    .'</definedName>';
            }
        }

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            .'<sheets>'.$sheets.'</sheets>'
            .($definedNames !== '' ? '<definedNames>'.$definedNames.'</definedNames>' : '')
            .'</workbook>';
    }

    private function workbookRelsXml(int $sheetCount): string
    {
        $relationships = '';

        for ($index = 1; $index <= $sheetCount; $index++) {
            $relationships .= '<Relationship Id="rId'.$index.'" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet'.$index.'.xml"/>';
        }

        $relationships .= '<Relationship Id="rId'.($sheetCount + 1).'" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>';

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            .$relationships
            .'</Relationships>';
    }

    private function stylesXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            .'<numFmts count="1"><numFmt numFmtId="164" formatCode="#,##0"/></numFmts>'
            .'<fonts count="8">'
            .'<font><sz val="11"/><color rgb="FF334155"/><name val="Calibri"/><family val="2"/></font>'
            .'<font><b/><sz val="16"/><color rgb="FF0F172A"/><name val="Calibri"/><family val="2"/></font>'
            .'<font><sz val="9"/><color rgb="FF64748B"/><name val="Calibri"/><family val="2"/></font>'
            .'<font><b/><sz val="11"/><color rgb="FFFFFFFF"/><name val="Calibri"/><family val="2"/></font>'
            .'<font><b/><sz val="11"/><color rgb="FF065F46"/><name val="Calibri"/><family val="2"/></font>'
            .'<font><b/><sz val="11"/><color rgb="FF991B1B"/><name val="Calibri"/><family val="2"/></font>'
            .'<font><b/><sz val="12"/><color rgb="FF0F172A"/><name val="Calibri"/><family val="2"/></font>'
            .'<font><b/><sz val="11"/><color rgb="FF0F172A"/><name val="Calibri"/><family val="2"/></font>'
            .'</fonts>'
            .'<fills count="6">'
            .'<fill><patternFill patternType="none"/></fill>'
            .'<fill><patternFill patternType="gray125"/></fill>'
            .'<fill><patternFill patternType="solid"><fgColor rgb="FF0F172A"/><bgColor indexed="64"/></patternFill></fill>'
            .'<fill><patternFill patternType="solid"><fgColor rgb="FFF8FAFC"/><bgColor indexed="64"/></patternFill></fill>'
            .'<fill><patternFill patternType="solid"><fgColor rgb="FFECFDF5"/><bgColor indexed="64"/></patternFill></fill>'
            .'<fill><patternFill patternType="solid"><fgColor rgb="FFFEF2F2"/><bgColor indexed="64"/></patternFill></fill>'
            .'</fills>'
            .'<borders count="3">'
            .'<border><left/><right/><top/><bottom/><diagonal/></border>'
            .'<border><left/><right/><top/><bottom style="thin"><color rgb="FFE2E8F0"/></bottom><diagonal/></border>'
            .'<border><left/><right/><top/><bottom style="medium"><color rgb="FFF7D60A"/></bottom><diagonal/></border>'
            .'</borders>'
            .'<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
            .'<cellXfs count="14">'
            .'<xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>'
            .'<xf numFmtId="0" fontId="1" fillId="0" borderId="2" xfId="0" applyFont="1" applyBorder="1" applyAlignment="1"><alignment vertical="center"/></xf>'
            .'<xf numFmtId="0" fontId="2" fillId="0" borderId="0" xfId="0" applyFont="1" applyAlignment="1"><alignment vertical="center"/></xf>'
            .'<xf numFmtId="0" fontId="3" fillId="2" borderId="2" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment vertical="center" wrapText="1"/></xf>'
            .'<xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyFont="1" applyBorder="1" applyAlignment="1"><alignment vertical="center"/></xf>'
            .'<xf numFmtId="164" fontId="0" fillId="0" borderId="1" xfId="0" applyNumberFormat="1" applyFont="1" applyBorder="1" applyAlignment="1"><alignment horizontal="right" vertical="center"/></xf>'
            .'<xf numFmtId="0" fontId="0" fillId="3" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment vertical="center"/></xf>'
            .'<xf numFmtId="164" fontId="0" fillId="3" borderId="1" xfId="0" applyNumberFormat="1" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="right" vertical="center"/></xf>'
            .'<xf numFmtId="0" fontId="4" fillId="4" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>'
            .'<xf numFmtId="0" fontId="5" fillId="5" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>'
            .'<xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyFont="1" applyBorder="1" applyAlignment="1"><alignment vertical="top" wrapText="1"/></xf>'
            .'<xf numFmtId="0" fontId="0" fillId="3" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment vertical="top" wrapText="1"/></xf>'
            .'<xf numFmtId="0" fontId="6" fillId="0" borderId="0" xfId="0" applyFont="1" applyAlignment="1"><alignment vertical="center"/></xf>'
            .'<xf numFmtId="0" fontId="7" fillId="0" borderId="1" xfId="0" applyFont="1" applyBorder="1" applyAlignment="1"><alignment vertical="center"/></xf>'
            .'</cellXfs>'
            .'<cellStyles count="1"><cellStyle name="Normal" xfId="0" builtinId="0"/></cellStyles>'
            .'</styleSheet>';
    }

    private function sheetXml(array $sheet, int $index): string
    {
        $blocks = $sheet['blocks'];
        $options = $sheet['options'];

        $maxColumns = 1;

        foreach ($blocks as $block) {
            $maxColumns = max($maxColumns, count($block['columns']));
        }

        $lastColumn = $this->columnLetter($maxColumns - 1);
        $rows = [];
        $merges = [];
        $row = 1;

        if (isset($options['title']) && $options['title'] !== '') {
            $rows[$row] = $this->rowXml($row, 26, [
                $this->textCell('A'.$row, (string) $options['title'], self::STYLE_TITLE),
            ]);

            if ($maxColumns > 1) {
                $merges[] = 'A'.$row.':'.$lastColumn.$row;
            }

            $row++;
        }

        if (isset($options['subtitle']) && $options['subtitle'] !== '') {
            $rows[$row] = $this->rowXml($row, 16, [
                $this->textCell('A'.$row, (string) $options['subtitle'], self::STYLE_SUBTITLE),
            ]);

            if ($maxColumns > 1) {
                $merges[] = 'A'.$row.':'.$lastColumn.$row;
            }

            $row++;
        }

        if ($rows !== []) {
            $row++;
        }

        $firstHeaderRow = null;
        $autoFilter = null;

        foreach ($blocks as $blockIndex => $block) {
            $columns = $block['columns'];

            if (isset($block['heading']) && $block['heading'] !== '') {
                $rows[$row] = $this->rowXml($row, 22, [
                    $this->textCell('A'.$row, (string) $block['heading'], self::STYLE_SECTION),
                ]);

                if ($maxColumns > 1) {
                    $merges[] = 'A'.$row.':'.$lastColumn.$row;
                }

                $row++;
            }

            $headerRow = $row;
            $headerCells = [];

            foreach ($columns as $columnIndex => $column) {
                $headerCells[] = $this->textCell(
                    $this->columnLetter($columnIndex).$row,
                    (string) $column['label'],
                    self::STYLE_HEADER
                );
            }

            $rows[$row] = $this->rowXml($row, 30, $headerCells);

            if ($firstHeaderRow === null) {
                $firstHeaderRow = $headerRow;
            }

            $row++;

            foreach (array_values($block['rows']) as $dataIndex => $dataRow) {
                $zebra = ! empty($options['zebra']) && $dataIndex % 2 === 1;
                $cells = [];
                $longest = 0;

                foreach ($columns as $columnIndex => $column) {
                    [$cellXml, $length] = $this->dataCell(
                        $this->columnLetter($columnIndex).$row,
                        $dataRow[$columnIndex] ?? null,
                        $column,
                        $zebra
                    );

                    $cells[] = $cellXml;
                    $longest = max($longest, $length);
                }

                $rows[$row] = $this->rowXml($row, $longest > 90 ? 30 : 20, $cells);
                $row++;
            }

            if ($blockIndex === 0 && ! empty($options['autofilter']) && $row > $headerRow + 1) {
                $autoFilter = 'A'.$headerRow.':'.$lastColumn.($row - 1);
            }

            $row++;
        }

        $lastRow = max(array_keys($rows));

        $sheetViews = '<sheetViews><sheetView'.($index === 0 ? ' tabSelected="1"' : '').' workbookViewId="0">';

        if (! empty($options['freeze']) && $firstHeaderRow !== null) {
            $topLeft = 'A'.($firstHeaderRow + 1);
            $sheetViews .= '<pane ySplit="'.$firstHeaderRow.'" topLeftCell="'.$topLeft.'" activePane="bottomLeft" state="frozen"/>';
            $sheetViews .= '<selection pane="bottomLeft" activeCell="'.$topLeft.'" sqref="'.$topLeft.'"/>';
        }

        $sheetViews .= '</sheetView></sheetViews>';

        $widths = [];

        foreach ($blocks as $block) {
            foreach ($block['columns'] as $columnIndex => $column) {
                $widths[$columnIndex] = max($widths[$columnIndex] ?? 0, (float) ($column['width'] ?? 12));
            }
        }

        ksort($widths);

        $cols = '<cols>';

        foreach ($widths as $columnIndex => $width) {
            $cols .= '<col min="'.($columnIndex + 1).'" max="'.($columnIndex + 1).'" width="'.$this->numberValue($width).'" customWidth="1"/>';
        }

        $cols .= '</cols>';

        $mergeXml = '';

        if ($merges !== []) {
            $mergeXml = '<mergeCells count="'.count($merges).'">';

            foreach ($merges as $merge) {
                $mergeXml .= '<mergeCell ref="'.$merge.'"/>';
            }

            $mergeXml .= '</mergeCells>';
        }

        $autoFilterXml = $autoFilter !== null ? '<autoFilter ref="'.$autoFilter.'"/>' : '';
        $orientation = ! empty($options['landscape']) ? 'landscape' : 'portrait';

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            .'<sheetPr><pageSetUpPr fitToPage="1"/></sheetPr>'
            .'<dimension ref="A1:'.$lastColumn.$lastRow.'"/>'
            .$sheetViews
            .'<sheetFormatPr defaultRowHeight="18"/>'
            .$cols
            .'<sheetData>'.implode('', $rows).'</sheetData>'
            .$autoFilterXml
            .$mergeXml
            .'<pageMargins left="0.4" right="0.4" top="0.5" bottom="0.5" header="0.3" footer="0.3"/>'
            .'<pageSetup paperSize="9" orientation="'.$orientation.'" fitToWidth="1" fitToHeight="0"/>'
            .'<headerFooter><oddFooter>&amp;C&amp;A - Halaman &amp;P dari &amp;N</oddFooter></headerFooter>'
            .'</worksheet>';
    }

    private function firstHeaderRow(array $sheet): int
    {
        $options = $sheet['options'];
        $row = 1;

        if (isset($options['title']) && $options['title'] !== '') {
            $row++;
        }

        if (isset($options['subtitle']) && $options['subtitle'] !== '') {
            $row++;
        }

        if ($row > 1) {
            $row++;
        }

        foreach ($sheet['blocks'] as $block) {
            if (isset($block['heading']) && $block['heading'] !== '') {
                $row++;
            }

            return $row;
        }

        return $row;
    }

    /**
     * @param  array<int, string>  $cells
     */
    private function rowXml(int $row, int $height, array $cells): string
    {
        return '<row r="'.$row.'" ht="'.$height.'" customHeight="1">'.implode('', $cells).'</row>';
    }

    private function textCell(string $reference, string $value, int $style): string
    {
        if ($value === '') {
            return '<c r="'.$reference.'" s="'.$style.'"/>';
        }

        return '<c r="'.$reference.'" s="'.$style.'" t="inlineStr"><is><t xml:space="preserve">'
            .$this->esc($value).'</t></is></c>';
    }

    /**
     * @param  array{label: string, width?: float|int, type?: string}  $column
     * @return array{0: string, 1: int}
     */
    private function dataCell(string $reference, mixed $cell, array $column, bool $zebra): array
    {
        $type = $column['type'] ?? 'text';
        $status = null;

        if (is_array($cell) && array_key_exists('value', $cell)) {
            $type = $cell['type'] ?? $type;
            $status = $cell['status'] ?? null;
            $cell = $cell['value'];
        }

        $value = $cell === null ? '' : $cell;

        if ($type === 'number' && $value !== '' && ! is_numeric($value)) {
            $type = 'text';
        }

        $style = match (true) {
            $type === 'status' => $status === 'low' ? self::STYLE_STATUS_LOW : self::STYLE_STATUS_OK,
            $type === 'number' => $zebra ? self::STYLE_NUMBER_ZEBRA : self::STYLE_NUMBER,
            $type === 'wrap' => $zebra ? self::STYLE_WRAP_ZEBRA : self::STYLE_WRAP,
            default => $zebra ? self::STYLE_TEXT_ZEBRA : self::STYLE_TEXT,
        };

        if ($value === '') {
            return ['<c r="'.$reference.'" s="'.$style.'"/>', 0];
        }

        if ($type === 'number') {
            return [
                '<c r="'.$reference.'" s="'.$style.'"><v>'.$this->numberValue($value).'</v></c>',
                0,
            ];
        }

        $text = (string) $value;

        return [
            '<c r="'.$reference.'" s="'.$style.'" t="inlineStr"><is><t xml:space="preserve">'
                .$this->esc($text).'</t></is></c>',
            mb_strlen($text),
        ];
    }

    private function numberValue(mixed $value): string
    {
        if (is_int($value)) {
            return (string) $value;
        }

        $number = (float) $value;

        if (floor($number) === $number && abs($number) < 9.0E15) {
            return (string) (int) $number;
        }

        return rtrim(rtrim(number_format($number, 6, '.', ''), '0'), '.');
    }

    private function columnLetter(int $index): string
    {
        $letters = '';
        $index++;

        while ($index > 0) {
            $mod = ($index - 1) % 26;
            $letters = chr(65 + $mod).$letters;
            $index = intdiv($index - 1, 26);
        }

        return $letters;
    }

    private function sanitizeSheetName(string $name): string
    {
        $name = preg_replace('/[\[\]:*?\/\\\\]/', ' ', $name) ?? $name;
        $name = trim($name);

        if ($name === '') {
            $name = 'Sheet';
        }

        return mb_substr($name, 0, 31);
    }

    private function quotedSheetName(string $name): string
    {
        return "'".str_replace("'", "''", $name)."'";
    }

    private function esc(string $value): string
    {
        if (! mb_check_encoding($value, 'UTF-8')) {
            $value = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
        }

        $value = preg_replace('/[^\x09\x0A\x0D\x20-\x{D7FF}\x{E000}-\x{FFFD}\x{10000}-\x{10FFFF}]/u', '', $value) ?? '';

        if (mb_strlen($value) > 32767) {
            $value = mb_substr($value, 0, 32767);
        }

        return htmlspecialchars($value, ENT_QUOTES | ENT_XML1, 'UTF-8');
    }
}
