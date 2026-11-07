<?php

namespace App\Core;

/**
 * Reads a flat "table of rows" out of an uploaded .csv or .xlsx file with no external library
 * (there's no Composer/vendor in this project). Only handles the common case a plain data export
 * produces: one sheet, a header row, simple string/number cells - no merged cells, formulas, or
 * multiple sheets.
 */
class SpreadsheetReader
{
    /**
     * @return array<int,array<string,string>> each row keyed by a normalized header
     *         (lowercase, trimmed, non-alphanumeric characters stripped)
     */
    public static function read(string $tmpPath, string $originalFilename): array
    {
        $ext = strtolower((string) pathinfo($originalFilename, PATHINFO_EXTENSION));

        return match ($ext) {
            'csv' => self::readCsv($tmpPath),
            'xlsx' => self::readXlsx($tmpPath),
            default => throw new \RuntimeException('Please upload a .csv or .xlsx file.'),
        };
    }

    private static function normalizeHeader(string $header): string
    {
        return preg_replace('/[^a-z0-9]/', '', strtolower(trim($header))) ?? '';
    }

    private static function readCsv(string $tmpPath): array
    {
        $handle = fopen($tmpPath, 'r');
        if ($handle === false) {
            throw new \RuntimeException('That CSV file could not be read.');
        }

        $headers = fgetcsv($handle);
        if ($headers === false) {
            fclose($handle);
            throw new \RuntimeException('That CSV file appears to be empty.');
        }
        $headers = array_map([self::class, 'normalizeHeader'], $headers);

        $rows = [];
        while (($cells = fgetcsv($handle)) !== false) {
            if ($cells === [null] || $cells === ['']) {
                continue;
            }
            $rows[] = self::zipRow($headers, $cells);
        }
        fclose($handle);

        return $rows;
    }

    /** @param string[] $headers @param array<int,string|null> $cells */
    private static function zipRow(array $headers, array $cells): array
    {
        $row = [];
        foreach ($headers as $i => $header) {
            if ($header === '') {
                continue;
            }
            $row[$header] = trim((string) ($cells[$i] ?? ''));
        }
        return $row;
    }

    private static function readXlsx(string $tmpPath): array
    {
        $zip = new \ZipArchive();
        if ($zip->open($tmpPath) !== true) {
            throw new \RuntimeException('That .xlsx file could not be opened - it may be corrupted or not a real Excel file.');
        }

        $sharedStrings = self::readSharedStrings($zip);
        $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');
        if ($sheetXml === false) {
            $sheetXml = self::findFirstSheet($zip);
        }
        $zip->close();

        if ($sheetXml === false || $sheetXml === null) {
            throw new \RuntimeException('No readable sheet was found in that .xlsx file.');
        }

        $sheet = @simplexml_load_string($sheetXml);
        if ($sheet === false) {
            throw new \RuntimeException('That .xlsx file\'s sheet data could not be parsed.');
        }

        $grid = [];
        $maxCol = 0;
        foreach ($sheet->sheetData->row as $rowEl) {
            $rowIndex = (int) $rowEl['r'];
            foreach ($rowEl->c as $cellEl) {
                $colIndex = self::columnLetterToIndex((string) $cellEl['r']);
                $maxCol = max($maxCol, $colIndex);
                $grid[$rowIndex][$colIndex] = self::cellValue($cellEl, $sharedStrings);
            }
        }

        if (!$grid) {
            return [];
        }

        $rowNumbers = array_keys($grid);
        sort($rowNumbers, SORT_NUMERIC);
        $headerRowNumber = $rowNumbers[0];

        $headers = [];
        for ($c = 0; $c <= $maxCol; $c++) {
            $headers[$c] = self::normalizeHeader((string) ($grid[$headerRowNumber][$c] ?? ''));
        }

        $rows = [];
        foreach ($rowNumbers as $rowNumber) {
            if ($rowNumber === $headerRowNumber) {
                continue;
            }
            $cells = [];
            for ($c = 0; $c <= $maxCol; $c++) {
                $cells[$c] = $grid[$rowNumber][$c] ?? '';
            }
            $zipped = self::zipRow($headers, $cells);
            if (array_filter($zipped, fn ($v) => $v !== '')) {
                $rows[] = $zipped;
            }
        }

        return $rows;
    }

    /** @return string[] index => shared string value */
    private static function readSharedStrings(\ZipArchive $zip): array
    {
        $xml = $zip->getFromName('xl/sharedStrings.xml');
        if ($xml === false) {
            return [];
        }
        $doc = @simplexml_load_string($xml);
        if ($doc === false) {
            return [];
        }

        $strings = [];
        foreach ($doc->si as $si) {
            // A shared string can be a single <t>, or several <r><t> "runs" (rich text) that
            // need concatenating back into one value.
            if (isset($si->t)) {
                $strings[] = (string) $si->t;
            } else {
                $text = '';
                foreach ($si->r as $run) {
                    $text .= (string) $run->t;
                }
                $strings[] = $text;
            }
        }
        return $strings;
    }

    private static function findFirstSheet(\ZipArchive $zip): ?string
    {
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $name = $zip->getNameIndex($i);
            if ($name !== false && preg_match('#^xl/worksheets/sheet\d+\.xml$#', $name)) {
                $xml = $zip->getFromName($name);
                return $xml === false ? null : $xml;
            }
        }
        return null;
    }

    /** "C7" -> 2 (zero-based column index) */
    private static function columnLetterToIndex(string $cellRef): int
    {
        $letters = preg_replace('/[0-9]/', '', $cellRef) ?? '';
        $index = 0;
        foreach (str_split($letters) as $char) {
            $index = $index * 26 + (ord(strtoupper($char)) - ord('A') + 1);
        }
        return $index - 1;
    }

    private static function cellValue(\SimpleXMLElement $cellEl, array $sharedStrings): string
    {
        $type = (string) $cellEl['t'];

        if ($type === 's') {
            $index = (int) $cellEl->v;
            return $sharedStrings[$index] ?? '';
        }
        if ($type === 'inlineStr') {
            return (string) $cellEl->is->t;
        }
        return (string) $cellEl->v;
    }
}
