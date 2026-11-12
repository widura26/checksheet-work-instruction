<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class BomDatasetImportEndpointTest extends TestCase
{
    use DatabaseTransactions;

    private const CSV = "Nama Sheet Sumber,Kode Material,kode Material Delete,Kode Material Stock,Material / Komponen,Spesifikasi,Qty / TS (Series),UoM/Car,Qty Total (Series) + Allowance,QTY PR TOTAL\n"
        . "Elektrik,MAT-001,,STK-001,Kabel,2x2.5,10,Pcs,12,5\n"
        . ",,,,,,9,,,\n"
        . "Bogie,,,,Cover,Plastik,2,Pcs,2,0\n";

    private function withImportAccess(): static
    {
        return $this
            ->actingAs(User::factory()->create())
            ->withoutMiddleware(VerifyCsrfToken::class);
    }

    public function test_import_endpoint_dispatches_and_inserts_rows(): void
    {
        $response = $this->withImportAccess()->post('/bom/import', [
            'file' => UploadedFile::fake()->createWithContent('rekap.csv', self::CSV),
        ]);

        $response->assertOk()->assertJson(['success' => true]);
        $importId = $response->json('import_id');

        $rows = DB::table('bill_of_material_dataset')->where('import_id', $importId)->get();
        $this->assertCount(2, $rows);

        $valid = $rows->firstWhere('kode_material', 'MAT-001');
        $this->assertSame('Elektrik', $valid->sheet_name);
        $this->assertSame('Pcs', $valid->uom_car);
        $this->assertSame('valid', $valid->status);

        $this->assertSame('invalid', $rows->firstWhere('sheet_name', 'Bogie')->status);
    }

    public function test_import_replaces_existing_rows(): void
    {
        DB::table('bill_of_material_dataset')->insert([
            'id' => (string) Str::uuid(),
            'sheet_name' => 'Lama',
            'kode_material' => 'OLD-1',
            'status' => 'valid',
            'import_id' => 'import-lama',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->withImportAccess()
            ->post('/bom/import', ['file' => UploadedFile::fake()->createWithContent('rekap.csv', self::CSV)])
            ->assertOk();

        $this->assertNull(DB::table('bill_of_material_dataset')->where('kode_material', 'OLD-1')->first());
        $this->assertSame(2, DB::table('bill_of_material_dataset')->count());
    }

    public function test_status_endpoint_reports_completed_with_count(): void
    {
        $response = $this->withImportAccess()->post('/bom/import', [
            'file' => UploadedFile::fake()->createWithContent('rekap.csv', self::CSV),
        ]);
        $importId = $response->json('import_id');

        $this->withImportAccess()
            ->get('/bom/status/' . $importId)
            ->assertOk()
            ->assertJson(['status' => 'completed', 'imported' => 2]);
    }

    public function test_status_endpoint_reports_failed_when_failed_job_exists(): void
    {
        $importId = (string) Str::uuid();
        DB::table('failed_jobs')->insert([
            'uuid' => (string) Str::uuid(),
            'connection' => 'database',
            'queue' => 'default',
            'payload' => '{"data":{"command":"O:8:\"stdClass\":0:{}","importId":"' . $importId . '"}}',
            'exception' => 'RuntimeException',
        ]);

        $this->withImportAccess()
            ->get('/bom/status/' . $importId)
            ->assertOk()
            ->assertJson(['status' => 'failed']);
    }

    public function test_import_rejects_disallowed_extension(): void
    {
        $this->withImportAccess()
            ->post('/bom/import', ['file' => UploadedFile::fake()->createWithContent('rekap.txt', 'a,b')])
            ->assertStatus(422);
    }
}
