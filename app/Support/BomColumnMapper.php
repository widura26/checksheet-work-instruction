<?php

namespace App\Support;

use Illuminate\Support\Str;

class BomColumnMapper
{
    private const ALIASES = [
        'sheet_name' => ['nama_sheet_sumber'],
        'kode_material' => ['kode_material'],
        'kode_material_delete' => ['kode_material_delete'],
        'kode_material_stock' => ['kode_material_stock'],
        'material_komponen' => ['material_komponen'],
        'spesifikasi' => ['spesifikasi'],
        'qty_ts_series' => ['qty_ts_series'],
        'uom_car' => ['uomcar', 'uom_car'],
        'qty_total_series_allowance' => ['qty_total_series_allowance'],
        'qty_pr_total' => ['qty_pr_total'],
    ];

    public static function normalize(string $header): string
    {
        return Str::slug(trim($header), '_');
    }

    public static function map(array $headers): array
    {
        $result = [];

        foreach ($headers as $header) {
            $normalized = self::normalize((string) $header);

            foreach (self::ALIASES as $dbColumn => $aliases) {
                if (in_array($normalized, $aliases, true)) {
                    $result[$normalized] = $dbColumn;
                    break;
                }
            }
        }

        return $result;
    }
}
