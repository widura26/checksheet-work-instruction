<?php

namespace App\Support;

use Generator;
use XMLReader;
use ZipArchive;

class ExcelReader {

    public static function sheets(string $path): array
    {
        $zip = new ZipArchive();
        if ($zip->open($path) !== true) {
            throw new \Exception('File xlsx tidak valid');
        }

        $wb   = simplexml_load_string($zip->getFromName('xl/workbook.xml'));
        $rels = simplexml_load_string($zip->getFromName('xl/_rels/workbook.xml.rels'));
        $zip->close();

        $targets = [];
        foreach ($rels->Relationship as $rel) {
            $targets[(string) $rel['Id']] = basename((string) $rel['Target'], '.xml');
        }

        $ns = 'http://schemas.openxmlformats.org/officeDocument/2006/relationships';
        $result = [];
        foreach ($wb->sheets->sheet as $sheet) {
            $rid = (string) $sheet->attributes($ns)['id'];
            $result[(string) $sheet['name']] = $targets[$rid];
        }

        return $result;
    }

    public static function rows(string $path, string $sheet = 'sheet1'): Generator {
        $strings = self::sharedStrings($path);

        $reader = new XMLReader();
        $reader->open("zip://{$path}#xl/worksheets/{$sheet}.xml");

        while ($reader->read()) {
            if ($reader->nodeType !== XMLReader::ELEMENT || $reader->name !== 'row') {
                continue;
            }

            $row = simplexml_load_string($reader->readOuterXml());
            $cells = [];

            foreach ($row->c as $c) {
                $col = preg_replace('/\d+/', '', (string) $c['r']);
                $type = (string) $c['t'];

                if ($type === 's') {
                    $value = $strings[(int) $c->v] ?? '';
                } elseif ($type === 'inlineStr') {
                    $value = (string) $c->is->t;
                } else {
                    $value = (string) $c->v;
                }

                $cells[$col] = $value;
            }

            yield (int) $row['r'] => $cells;
        }

        $reader->close();
    }

    private static function sharedStrings(string $path): array
    {
        $strings = [];
        $reader = new XMLReader();

        if (! @$reader->open("zip://{$path}#xl/sharedStrings.xml")) {
            return $strings;
        }

        while ($reader->read()) {
            if ($reader->nodeType === XMLReader::ELEMENT && $reader->name === 'si') {
                $si = simplexml_load_string($reader->readOuterXml());
                $text = isset($si->t)
                    ? (string) $si->t
                    : implode('', array_map(fn ($r) => (string) $r->t, iterator_to_array($si->r, false)));
                $strings[] = $text;
            }
        }

        $reader->close();

        return $strings;
    }

}

?>
