<?php

namespace Tests\Feature;

use App\Http\Controllers\SapDatasetController;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class ResetDataTest extends TestCase
{
    use DatabaseTransactions;

    public function test_reset_data_clears_rows_and_invalidates_cached_dataset(): void
    {
        DB::table('daftar_kebutuhan_materials')->insert([
            'id' => (string) Str::uuid(),
            'wbs' => 'WBS-1',
            'sheet_asal' => 'PIECE PART',
            'no' => '1',
            'rev' => '',
            'kode_material' => 'PP-1',
            'kode_material_delete' => '',
            'deskripsi_material' => 'BOTTOM PLATE',
            'satuan' => 'PCS',
            'group' => 'PIECE PART',
            'lot' => 'TANPA TRAINSET',
            'kebutuhan' => '1',
            'sum' => '1',
            'sudah_pr' => '0',
            'belum_pr' => '0',
            'tindak_lanjut' => 'PR BARU',
            'no_pr' => 'PR-1',
        ]);

        $countBefore = DB::table('daftar_kebutuhan_materials')->count();
        $this->assertGreaterThan(0, $countBefore);

        $versionBefore = (int) Cache::get('mp:version', 1);

        $response = app(SapDatasetController::class)->resetData();

        $this->assertTrue($response->getData(true)['success']);
        $this->assertSame(0, DB::table('daftar_kebutuhan_materials')->count());
        $this->assertSame($versionBefore + 1, (int) Cache::get('mp:version', 1));
    }
}
