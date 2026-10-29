<?php

namespace App\Support\Xlsx;

use InvalidArgumentException;
use SimpleXMLElement;
use ZipArchive;

/**
 * Pembaca XLSX ringan untuk kebutuhan impor.
 *
 * Hanya membaca sheet pertama dan mengembalikan baris sebagai array string.
 */
class XlsxReader
{
    private const MAIN_NS = 'http://schemas.openxmlformats.org/spreadsheetml/2006/main';

    private const REL_NS = 'http://schemas.openxmlformats.org/officeDocument/2006/relationships';

    private const PACKAGE_REL_NS = 'http://schemas.openxmlformats.org/package/2006/relationships';

    /**
     * @return array<int, array<int, string|null>>
     */
    public function rows(string $path, int $maxRows = 10000, int $maxColumns = 100): array
    {
        if (! is_file($path)) {
            throw new InvalidArgumentException('File tidak ditemukan.');
        }

        $zip = new ZipArchive;

        if ($zip->open($path) !== true) {
            throw new InvalidArgumentException('File Excel tidak valid.');
        }

        try {
            $this->guardArchive($zip);

            $sheetPath = $this->firstSheetPath($zip);
            $sharedStrings = $this->sharedStrings($zip);

            $sheetXml = $zip->getFromName($sheetPath);

            if ($sheetXml === false) {
                throw new InvalidArgumentException('Worksheet tidak ditemukan di file Excel.');
            }

            return $this->parseSheet($sheetXml, $sharedStrings, $maxRows, $maxColumns);
        } finally {
            $zip->close();
        }
    }

    private function guardArchive(ZipArchive $zip): void
    {
        if ($zip->numFiles > 1000) {
            throw new InvalidArgumentException('File Excel memiliki terlalu banyak entri.');
        }

        $totalSize = 0;

        for ($index = 0; $index < $zip->numFiles; $index++) {
            $stat = $zip->statIndex($index);

            if ($stat === false) {
                continue;
            }

            if ($stat['size'] > 20 * 1024 * 1024) {
                throw new InvalidArgumentException('Ukuran isi file Excel terlalu besar.');
            }

            $totalSize += $stat['size'];
        }

        if ($totalSize > 50 * 1024 * 1024) {
            throw new InvalidArgumentException('Ukuran isi file Excel terlalu besar.');
        }

        if ($zip->locateName('[Content_Types].xml') === false || $zip->locateName('xl/workbook.xml') === false) {
            throw new InvalidArgumentException('File bukan workbook Excel yang valid.');
        }
    }

    private function firstSheetPath(ZipArchive $zip): string
    {
        $workbookXml = $zip->getFromName('xl/workbook.xml');
        $relsXml = $zip->getFromName('xl/_rels/workbook.xml.rels');

        if ($workbookXml !== false && $relsXml !== false) {
            $workbook = $this->loadXml($workbookXml);
            $rels = $this->loadXml($relsXml);

            $sheetNodes = $this->xpath($workbook, '//m:sheets/m:sheet');

            if ($sheetNodes !== []) {
                $attributes = $sheetNodes[0]->attributes(self::REL_NS);
                $relationshipId = isset($attributes['id']) ? (string) $attributes['id'] : '';

                if ($relationshipId !== '') {
                    foreach ($this->xpath($rels, '//p:Relationship', 'p', self::PACKAGE_REL_NS) as $relationship) {
                        if ((string) $relationship['Id'] === $relationshipId) {
                            return $this->normalizeTarget((string) $relationship['Target']);
                        }
                    }
                }
            }
        }

        for ($index = 0; $index < $zip->numFiles; $index++) {
            $name = $zip->getNameIndex($index);

            if (is_string($name) && preg_match('#^xl/worksheets/[^/]+\.xml$#', $name) === 1) {
                return $name;
            }
        }

        throw new InvalidArgumentException('Worksheet tidak ditemukan di file Excel.');
    }

    private function normalizeTarget(string $target): string
    {
        $target = ltrim($target, '/');

        if (str_starts_with($target, 'xl/')) {
            return $target;
        }

        $parts = [];

        foreach (explode('/', 'xl/'.$target) as $part) {
            if ($part === '' || $part === '.') {
                continue;
            }

            if ($part === '..') {
                array_pop($parts);

                continue;
            }

            $parts[] = $part;
        }

        return implode('/', $parts);
    }

    /**
     * @return array<int, string>
     */
    private function sharedStrings(ZipArchive $zip): array
    {
        $xml = $zip->getFromName('xl/sharedStrings.xml');

        if ($xml === false) {
            return [];
        }

        $root = $this->loadXml($xml);
        $strings = [];

        foreach ($this->xpath($root, '//m:si') as $item) {
            $text = '';

            foreach ($this->xpath($item, './/m:t') as $node) {
                $text .= (string) $node;
            }

            $strings[] = $text;
        }

        return $strings;
    }

    /**
     * @param  array<int, string>  $sharedStrings
     * @return array<int, array<int, string|null>>
     */
    private function parseSheet(string $xml, array $sharedStrings, int $maxRows, int $maxColumns): array
    {
        $root = $this->loadXml($xml);
        $rowNodes = $this->xpath($root, '//m:sheetData/m:row');

        if (count($rowNodes) > $maxRows) {
            throw new InvalidArgumentException('File Excel terlalu besar (maksimal '.number_format($maxRows, 0, ',', '.').' baris).');
        }

        $raw = [];

        foreach ($rowNodes as $rowNode) {
            $rowNumber = isset($rowNode['r']) ? max(1, (int) $rowNode['r']) : count($raw) + 1;
            $rowIndex = $rowNumber - 1;

            if ($rowIndex >= $maxRows) {
                break;
            }

            $cells = [];

            foreach ($this->xpath($rowNode, 'm:c') as $cell) {
                $reference = (string) $cell['r'];
                $columnIndex = $this->columnIndex($reference);

                if ($columnIndex === null || $columnIndex >= $maxColumns) {
                    continue;
                }

                $cells[$columnIndex] = $this->cellValue($cell, $sharedStrings);
            }

            $raw[$rowIndex] = $cells;
        }

        if ($raw === []) {
            return [];
        }

        ksort($raw);
        $rows = [];
        $lastRow = max(array_keys($raw));

        for ($rowIndex = 0; $rowIndex <= $lastRow && $rowIndex < $maxRows; $rowIndex++) {
            $cells = $raw[$rowIndex] ?? [];

            if ($cells !== []) {
                ksort($cells);
            }

            $lastColumn = $cells !== [] ? max(array_keys($cells)) : -1;
            $line = [];

            for ($columnIndex = 0; $columnIndex <= $lastColumn && $columnIndex < $maxColumns; $columnIndex++) {
                $line[$columnIndex] = $cells[$columnIndex] ?? null;
            }

            $rows[$rowIndex] = $line;
        }

        return $rows;
    }

    /**
     * @param  array<int, string>  $sharedStrings
     */
    private function cellValue(SimpleXMLElement $cell, array $sharedStrings): ?string
    {
        $type = isset($cell['t']) ? (string) $cell['t'] : '';

        if ($type === 'inlineStr') {
            $text = '';

            foreach ($this->xpath($cell, './/m:t') as $node) {
                $text .= (string) $node;
            }

            return $text;
        }

        if ($type === 's') {
            $index = (int) $this->firstValue($cell);

            return $sharedStrings[$index] ?? null;
        }

        if ($type === 'str') {
            return $this->firstValue($cell);
        }

        if ($type === 'b') {
            return $this->firstValue($cell) === '1' ? '1' : '0';
        }

        if ($type === 'e') {
            return null;
        }

        $value = $this->firstValue($cell);

        return $value === '' ? null : $value;
    }

    private function firstValue(SimpleXMLElement $cell): string
    {
        foreach ($cell->children(self::MAIN_NS) as $child) {
            if ($child->getName() === 'v') {
                return (string) $child;
            }
        }

        return '';
    }

    private function columnIndex(string $reference): ?int
    {
        if (preg_match('/^([A-Z]+)\d+$/', strtoupper($reference), $matches) !== 1) {
            return null;
        }

        $letters = $matches[1];
        $index = 0;

        for ($position = 0; $position < strlen($letters); $position++) {
            $index = $index * 26 + (ord($letters[$position]) - 64);
        }

        return $index - 1;
    }

    private function loadXml(string $xml): SimpleXMLElement
    {
        $previous = libxml_use_internal_errors(true);
        $root = simplexml_load_string($xml, SimpleXMLElement::class, LIBXML_NONET | LIBXML_COMPACT);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        if ($root === false) {
            throw new InvalidArgumentException('File Excel tidak dapat dibaca.');
        }

        return $root;
    }

    /**
     * @return array<int, SimpleXMLElement>
     */
    private function xpath(SimpleXMLElement $node, string $expression, string $prefix = 'm', string $namespace = self::MAIN_NS): array
    {
        $node->registerXPathNamespace($prefix, $namespace);

        return $node->xpath($expression) ?: [];
    }
}
