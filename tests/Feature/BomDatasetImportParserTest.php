<?php

namespace Tests\Feature;

use App\Imports\BomDatasetImport;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;

class BomDatasetImportParserTest extends TestCase
{
    use DatabaseTransactions;

    private const CSV = "Nama Sheet Sumber,Kode Material,kode Material Delete,Kode Material Stock,Material / Komponen,Spesifikasi,Qty / TS (Series),UoM/Car,Qty Total (Series) + Allowance,QTY PR TOTAL\n"
        . "Elektrik,MAT-001,,STK-001,Kabel,2x2.5,10,Pcs,12,5\n"
        . ",,,,,,9,,,\n"
        . "Bogie,,,,Cover,Plastik,2,Pcs,2,0\n";

    private string $filePath;

    protected function setUp(): void
    {
        parent::setUp();
        $this->filePath = sys_get_temp_dir() . '/bom-parser-' . Str::uuid() . '.csv';
    }

    protected function tearDown(): void
    {
        if (file_exists($this->filePath)) {
            unlink($this->filePath);
        }
        parent::tearDown();
    }

    private function insertExistingRow(string $kodeMaterial): void
    {
        DB::table('bill_of_material_dataset')->insert([
            'id' => (string) Str::uuid(),
            'sheet_name' => 'Lama',
            'kode_material' => $kodeMaterial,
            'status' => 'valid',
            'import_id' => 'import-lama',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function test_parses_rekap_csv_and_marks_statuses(): void
    {
        file_put_contents($this->filePath, self::CSV);
        $importId = (string) Str::uuid();

        Excel::import(new BomDatasetImport($importId), $this->filePath);

        $rows = DB::table('bill_of_material_dataset')->get();
        $this->assertCount(2, $rows);

        $valid = $rows->firstWhere('kode_material', 'MAT-001');
        $this->assertNotNull($valid);
        $this->assertSame($importId, $valid->import_id);
        $this->assertSame('Elektrik', $valid->sheet_name);
        $this->assertSame('', $valid->kode_material_delete ?? '');
        $this->assertSame('STK-001', $valid->kode_material_stock);
        $this->assertSame('Kabel', $valid->material_komponen);
        $this->assertSame('2x2.5', $valid->spesifikasi);
        $this->assertEquals('10', $valid->qty_ts_series);
        $this->assertSame('Pcs', $valid->uom_car);
        $this->assertEquals('12', $valid->qty_total_series_allowance);
        $this->assertEquals('5', $valid->qty_pr_total);
        $this->assertSame('valid', $valid->status);

        $invalid = $rows->firstWhere('sheet_name', 'Bogie');
        $this->assertNotNull($invalid);
        $this->assertNull($invalid->kode_material);
        $this->assertSame('invalid', $invalid->status);

        $this->assertNull($rows->firstWhere('qty_ts_series', '9'));
    }

    public function test_replaces_all_existing_rows(): void
    {
        $this->insertExistingRow('OLD-1');
        file_put_contents($this->filePath, self::CSV);

        Excel::import(new BomDatasetImport((string) Str::uuid()), $this->filePath);

        $this->assertNull(DB::table('bill_of_material_dataset')->where('kode_material', 'OLD-1')->first());
        $this->assertSame(2, DB::table('bill_of_material_dataset')->count());
    }

    public function test_unrecognized_header_fails_and_preserves_existing_data(): void
    {
        $this->insertExistingRow('OLD-1');
        file_put_contents($this->filePath, "Foo,Bar\nbaz,qux\n");

        $caught = null;
        try {
            Excel::import(new BomDatasetImport((string) Str::uuid()), $this->filePath);
        } catch (\RuntimeException $e) {
            $caught = $e;
        }

        $this->assertNotNull($caught, 'Parser harus melempar RuntimeException untuk file tanpa Kode Material');
        $this->assertStringContainsString('Kode Material', $caught->getMessage());
        $this->assertNotNull(
            DB::table('bill_of_material_dataset')->where('kode_material', 'OLD-1')->first(),
            'Data lama tidak boleh hilang saat import gagal'
        );
    }
}
