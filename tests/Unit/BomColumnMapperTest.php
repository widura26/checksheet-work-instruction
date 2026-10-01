<?php

namespace Tests\Unit;

use App\Support\BomColumnMapper;
use PHPUnit\Framework\TestCase;

class BomColumnMapperTest extends TestCase
{
    public function test_maps_all_ten_official_headers(): void
    {
        $map = BomColumnMapper::map([
            'Nama Sheet Sumber',
            'Kode Material',
            'kode Material Delete',
            'Kode Material Stock',
            'Material / Komponen',
            'Spesifikasi',
            'Qty / TS (Series)',
            'UoM/Car',
            'Qty Total (Series) + Allowance',
            'QTY PR TOTAL',
        ]);

        $this->assertSame([
            'nama_sheet_sumber' => 'sheet_name',
            'kode_material' => 'kode_material',
            'kode_material_delete' => 'kode_material_delete',
            'kode_material_stock' => 'kode_material_stock',
            'material_komponen' => 'material_komponen',
            'spesifikasi' => 'spesifikasi',
            'qty_ts_series' => 'qty_ts_series',
            'uomcar' => 'uom_car',
            'qty_total_series_allowance' => 'qty_total_series_allowance',
            'qty_pr_total' => 'qty_pr_total',
        ], $map);
    }

    public function test_normalize_is_idempotent_on_slugified_headers(): void
    {
        $this->assertSame('qty_pr_total', BomColumnMapper::normalize('  Qty PR   Total '));
        $this->assertSame('uomcar', BomColumnMapper::normalize('UoM/Car'));
        $this->assertSame('uomcar', BomColumnMapper::normalize('uomcar'));
    }

    public function test_ignores_unknown_and_empty_headers(): void
    {
        $map = BomColumnMapper::map(['Kode Material', 'Keterangan Dari PR', '', 4]);

        $this->assertSame(['kode_material' => 'kode_material'], $map);
    }
}
