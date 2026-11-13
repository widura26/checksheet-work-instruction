<?php

namespace Tests\Feature;

use App\Models\User;
use App\Support\ImportTracker;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class SapDatasetImportStatusTest extends TestCase
{
    use DatabaseTransactions;

    private function actingUser(): static
    {
        return $this->actingAs(User::factory()->create());
    }

    public function test_status_rejects_invalid_import_id(): void
    {
        $this->actingUser()
            ->get('/material-planning/import-status/not-a-uuid')
            ->assertStatus(422)
            ->assertJson(['status' => 'invalid']);
    }

    public function test_status_returns_not_found_for_unknown_import_id(): void
    {
        $this->actingUser()
            ->get('/material-planning/import-status/' . Str::uuid())
            ->assertStatus(404)
            ->assertJson(['status' => 'not_found']);
    }

    public function test_status_returns_tracked_state(): void
    {
        $importId = (string) Str::uuid();
        ImportTracker::start($importId);
        ImportTracker::completed($importId, 5);

        $this->actingUser()
            ->get('/material-planning/import-status/' . $importId)
            ->assertOk()
            ->assertJson([
                'import_id' => $importId,
                'status' => 'completed',
                'imported' => 5,
            ]);
    }

    public function test_completed_import_invalidates_material_planning_cache(): void
    {
        Cache::forever('mp:version', 7);

        ImportTracker::completed((string) Str::uuid(), 1);

        $this->assertSame(8, (int) Cache::get('mp:version'));
    }

    public function test_import_excel_validates_file_before_processing(): void
    {
        $this->actingUser()
            ->post('/material-planning/import-excel', [])
            ->assertStatus(422);
    }

    public function test_data_endpoint_returns_paginated_rows_and_summary(): void
    {
        $response = $this->actingUser()->getJson('/material-planning/data?per_page=5');

        $response->assertOk()->assertJsonStructure([
            'data',
            'meta' => ['page', 'per_page', 'total', 'last_page'],
            'summary' => [
                'totalItem',
                'groupedItem',
                'jumlahMaterial',
                'statusMatrix',
                'statusTotals',
                'groupCounts',
                'groupLabels',
            ],
        ]);

        $this->assertLessThanOrEqual(5, count($response->json('data')));
        $this->assertSame(
            $response->json('meta.total'),
            $response->json('summary.groupedItem')
        );
    }

    public function test_summary_counts_raw_rows_separately_from_grouped_rows(): void
    {
        $summary = app(\App\Services\DataProcessService::class)->summarize(collect([
            ['group' => 'KOMPONEN', 'status_pr' => 'SUDAH PR', 'kode_material' => 'M1', 'raw_count' => 3],
            ['group' => 'PIECE PART', 'status_pr' => 'BELUM PR', 'kode_material' => 'M2', 'raw_count' => 2],
        ]));

        $this->assertSame(5, $summary['totalItem']);
        $this->assertSame(2, $summary['groupedItem']);
        $this->assertSame(3, $summary['statusMatrix']['SUDAH PR']['komponen']);
        $this->assertSame(2, $summary['statusMatrix']['BELUM PR']['piece_part']);
        $this->assertSame(2, $summary['statusTotals']['BELUM PR']);
        $this->assertSame(['KOMPONEN' => 3, 'PIECE PART' => 2], $summary['groupCounts']);
    }

    public function test_group_without_tindak_lanjut_is_included(): void
    {
        DB::table('daftar_kebutuhan_materials')->insert([
            'id' => (string) Str::uuid(),
            'wbs' => 'WBS-1',
            'sheet_asal' => 'PIECE PART',
            'no' => '56',
            'rev' => '',
            'kode_material' => 'PP-1',
            'kode_material_delete' => '',
            'deskripsi_material' => 'BOTTOM PLATE',
            'satuan' => '',
            'group' => 'PIECE PART',
            'lot' => 'TANPA TRAINSET',
            'kebutuhan' => '',
            'sum' => '',
            'sudah_pr' => '',
            'belum_pr' => '',
            'tindak_lanjut' => '',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $service = app(\App\Services\DataProcessService::class);

        $this->assertContains('PIECE PART', $service->filterOptions()['group']);

        $row = collect($service->dataset('Semua', 'PIECE PART', 'TANPA TRAINSET'))
            ->firstWhere('kode_material', 'PP-1');

        $this->assertNotNull($row);
        $this->assertSame('BELUM PR', $row['status_pr']);
        $this->assertSame(1, $row['raw_count']);
    }

    public function test_dataset_cache_is_keyed_by_data_version(): void
    {
        $service = app(\App\Services\DataProcessService::class);
        $service->dataset('Semua', 'Semua', 'Semua');

        $version = (int) Cache::get('mp:version', 1);
        $key = fn (int $v) => 'mp:dataset:v' . $v . ':' . md5('Semua|Semua|Semua');

        $this->assertTrue(Cache::has($key($version)));

        ImportTracker::completed((string) Str::uuid(), 1);

        $this->assertFalse(Cache::has($key($version + 1)));
    }

    public function test_export_streams_csv_without_removed_conversion_column(): void
    {
        $response = $this->actingUser()->get('/material-planning/export');

        $response->assertOk();

        $content = $response->streamedContent();

        $this->assertStringContainsString('Status PR', $content);
        $this->assertStringNotContainsString('Kode Material Konversi', $content);
        $this->assertStringNotContainsString('kode_konversi', $content);
    }

    public function test_import_queue_tracks_completion(): void
    {
        $csv = "WBS Elem,WBS Description,Purch.Req.,Material,Material Description\n"
            . "WBS-1,Deskripsi,PR-1,MAT-1,Kabel\n";

        $response = $this->actingUser()
            ->withoutMiddleware(VerifyCsrfToken::class)
            ->post('/material-planning/import-queue', [
                'file' => UploadedFile::fake()->createWithContent('sap.csv', $csv),
            ]);

        $response->assertOk()->assertJson(['success' => true]);

        $this->actingUser()
            ->get('/material-planning/import-status/' . $response->json('import_id'))
            ->assertOk()
            ->assertJson([
                'status' => 'completed',
                'imported' => 1,
            ]);
    }
}
