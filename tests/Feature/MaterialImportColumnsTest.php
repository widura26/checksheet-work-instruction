<?php

namespace Tests\Feature;

use App\Exports\SapDatasetExport;
use App\Jobs\ImportExcelJob;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;

class MaterialImportColumnsTest extends TestCase
{
    use DatabaseTransactions;

    public function test_import_maps_all_eighteen_material_columns_by_header(): void
    {
        $headers = [
            'WBS', 'Sheet Asal', 'NO', 'REV', 'Kode Material', 'Kode Material Konversi',
            'Kode Material Delete', 'Deskripsi Material', 'Group', 'Lot', 'Kebutuhan',
            'Tindak Lanjut', 'NO PR', 'Qty PR', 'Satuan', 'Deliv Date SAP', 'Status', 'NO PO',
        ];

        $values = [
            'WBS-1', 'SHEET-A', '42', 'B', 'MAT-1', 'KONV-1', 'DEL-1',
            'DESKRIPSI', 'KOMPONEN', 'TS 1', '7', 'PR BARU', 'PR-1', '5',
            'PCS', '2026-11-23', 'SUDAH PR', 'PO-1',
        ];

        $row = array_combine($headers, $values);
        $path = 'imports/material-columns-' . Str::uuid() . '.xlsx';

        Excel::store(
            new SapDatasetExport(new Collection([$row]), $headers),
            $path,
            'local'
        );

        ImportExcelJob::dispatchSync((string) Str::uuid(), $path);

        $imported = DB::table('daftar_kebutuhan_materials')
            ->where('kode_material', 'MAT-1')
            ->first();

        $this->assertNotNull($imported);
        $this->assertSame('WBS-1', $imported->wbs);
        $this->assertSame('SHEET-A', $imported->sheet_asal);
        $this->assertSame('42', $imported->no);
        $this->assertSame('B', $imported->rev);
        $this->assertSame('KONV-1', $imported->kode_material_konversi);
        $this->assertSame('DEL-1', $imported->kode_material_delete);
        $this->assertSame('DESKRIPSI', $imported->deskripsi_material);
        $this->assertSame('KOMPONEN', $imported->group);
        $this->assertSame('TS 1', $imported->lot);
        $this->assertSame('7', $imported->kebutuhan);
        $this->assertSame('PR BARU', $imported->tindak_lanjut);
        $this->assertSame('PR-1', $imported->no_pr);
        $this->assertSame('5', $imported->qty_pr);
        $this->assertSame('PCS', $imported->satuan);
        $this->assertSame('2026-11-23', $imported->deliv_date_sap);
        $this->assertSame('SUDAH PR', $imported->status);
        $this->assertSame('PO-1', $imported->no_po);
    }

    public function test_import_ignores_unknown_headers_and_column_order(): void
    {
        $headers = ['Catatan', 'NO PO', 'WBS', 'KODE  MATERIAL', 'Lot', 'Qty PR', 'Status'];
        $values = ['diabaikan', 'PO-2', 'WBS-2', 'MAT-2', 'TS 2', '9', 'BELUM PR'];

        $row = array_combine($headers, $values);
        $path = 'imports/material-header-order-' . Str::uuid() . '.xlsx';

        Excel::store(
            new SapDatasetExport(new Collection([$row]), $headers),
            $path,
            'local'
        );

        ImportExcelJob::dispatchSync((string) Str::uuid(), $path);

        $imported = DB::table('daftar_kebutuhan_materials')
            ->where('kode_material', 'MAT-2')
            ->first();

        $this->assertNotNull($imported);
        $this->assertSame('WBS-2', $imported->wbs);
        $this->assertSame('TS 2', $imported->lot);
        $this->assertSame('9', $imported->qty_pr);
        $this->assertSame('BELUM PR', $imported->status);
        $this->assertSame('PO-2', $imported->no_po);
        $this->assertSame('', $imported->no_pr);
    }
}
