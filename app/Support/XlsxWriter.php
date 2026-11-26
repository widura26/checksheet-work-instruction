<?php

namespace App\Support;

use RuntimeException;

/**
 * Penulis XLSX sederhana untuk ekspor tabular.
 *
 * Tidak memakai PhpSpreadsheet/ZipStream karena ZipStream v3 hanya mendukung
 * PHP 64-bit dan akan melempar "Cannot assign float to property
 * ZipStream\File::$crc of type int" pada PHP 32-bit.
 *
 * CRC dihitung dengan crc32() yang menghasilkan int (bertanda di 32-bit),
 * sehingga pack('V', ...) tetap menghasilkan 4 byte yang benar.
 */
class XlsxWriter
{
    /**
     * @param iterable<array<int, mixed>> $rows Baris pertama dipakai sebagai header.
     * @return string Path file sementara hasil ekspor.
     */
    public static function write(iterable $rows): string
    {
        $sheetRows = [];

        foreach ($rows as $index => $row) {
            $sheetRows[] = self::buildRow((int) $index + 1, (array) $row);
        }

        $parts = [
            '[Content_Types].xml' => self::contentTypes(),
            '_rels/.rels' => self::rootRelationships(),
            'xl/workbook.xml' => self::workbook(),
            'xl/_rels/workbook.xml.rels' => self::workbookRelationships(),
            'xl/styles.xml' => self::styles(),
            'xl/worksheets/sheet1.xml' => self::worksheet($sheetRows),
        ];

        $path = tempnam(sys_get_temp_dir(), 'xlsx');
        if ($path === false) {
            throw new RuntimeException('Gagal membuat file sementara untuk ekspor XLSX.');
        }

        if (class_exists(\ZipArchive::class)) {
            self::writeWithZipArchive($path, $parts);
        } else {
            self::writeZipManually($path, $parts);
        }

        return $path;
    }

    /** @param array<string, string> $parts */
    private static function writeWithZipArchive(string $path, array $parts): void
    {
        $zip = new \ZipArchive();

        if ($zip->open($path, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            throw new RuntimeException('Gagal membuka arsip XLSX.');
        }

        foreach ($parts as $name => $content) {
            if (! $zip->addFromString($name, $content)) {
                throw new RuntimeException("Gagal menulis $name ke arsip XLSX.");
            }
        }

        $zip->close();
    }

    /**
     * Fallback bila ekstensi zip tidak tersedia. Menulis ZIP tanpa kompresi
     * memakai crc32() agar tetap aman pada PHP 32-bit.
     *
     * @param array<string, string> $parts
     */
    private static function writeZipManually(string $path, array $parts): void
    {
        $local = '';
        $central = '';
        $offset = 0;
        $count = 0;

        foreach ($parts as $name => $content) {
            $crc = crc32($content);
            $length = strlen($content);
            $nameLength = strlen($name);

            $local .= pack(
                'VvvvvvVVVvv',
                0x04034B50, 20, 0, 0, 0, 0,
                $crc, $length, $length, $nameLength, 0
            ) . $name . $content;

            $central .= pack(
                'VvvvvvvVVVvvvvvVV',
                0x02014B50, 20, 20, 0, 0, 0, 0,
                $crc, $length, $length, $nameLength, 0, 0, 0, 0, 0,
                $offset
            ) . $name;

            $offset += 30 + $nameLength + $length;
            $count++;
        }

        $centralSize = strlen($central);

        file_put_contents($path, $local . $central . pack(
            'VvvvvVVv',
            0x06054B50, 0, 0, $count, $count, $centralSize, $offset, 0
        ));
    }

    /** @param array<int, mixed> $cells */
    private static function buildRow(int $rowNumber, array $cells): string
    {
        $columns = '';

        foreach (array_values($cells) as $columnIndex => $value) {
            $reference = self::columnLetter($columnIndex + 1) . $rowNumber;

            if ($value === null) {
                continue;
            }

            if (is_int($value) || (is_float($value) && is_finite($value))) {
                $columns .= '<c r="' . $reference . '"><v>' . $value . '</v></c>';
                continue;
            }

            $text = self::escape((string) $value);
            $columns .= '<c r="' . $reference . '" t="inlineStr"><is><t'
                . (preg_match('/^\s|\s$/', $text) ? ' xml:space="preserve"' : '')
                . '>' . $text . '</t></is></c>';
        }

        return '<row r="' . $rowNumber . '">' . $columns . '</row>';
    }

    private static function columnLetter(int $index): string
    {
        $letter = '';

        while ($index > 0) {
            $index--;
            $letter = chr(65 + ($index % 26)) . $letter;
            $index = intdiv($index, 26);
        }

        return $letter;
    }

    private static function escape(string $value): string
    {
        // Karakter kontrol di bawah 0x20 tidak diperbolehkan dalam XML.
        $value = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/u', '', $value) ?? '';

        return htmlspecialchars($value, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }

    /** @param array<int, string> $sheetRows */
    private static function worksheet(array $sheetRows): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            . '<sheetData>' . implode('', $sheetRows) . '</sheetData>'
            . '</worksheet>';
    }

    private static function contentTypes(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            . '<Default Extension="xml" ContentType="application/xml"/>'
            . '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
            . '<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
            . '<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'
            . '</Types>';
    }

    private static function rootRelationships(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
            . '</Relationships>';
    }

    private static function workbook(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            . '<sheets><sheet name="Data" sheetId="1" r:id="rId1"/></sheets>'
            . '</workbook>';
    }

    private static function workbookRelationships(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
            . '<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>'
            . '</Relationships>';
    }

    private static function styles(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            . '<fonts count="1"><font><sz val="11"/><name val="Calibri"/></font></fonts>'
            . '<fills count="1"><fill><patternFill patternType="none"/></fill></fills>'
            . '<borders count="1"><border/></borders>'
            . '<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
            . '<cellXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/></cellXfs>'
            . '</styleSheet>';
    }
}
