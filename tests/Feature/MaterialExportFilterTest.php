<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class MaterialExportFilterTest extends TestCase
{
    use DatabaseTransactions;

    private const WBS = 'WBS-EXPORT-TEST';

    private function actingUser(): static
    {
        $user = User::factory()->create();
        $user->assignRole(Role::findOrCreate('admin', 'web'));

        return $this->actingAs($user);
    }

    protected function setUp(): void
    {
        parent::setUp();

        foreach ([
            ['MAT-1', 'BAIK', 'PR BARU'],
            ['MAT-2', 'JELEK', 'BELUM PR'],
            ['MAT-3', 'BAIK', 'MENGGUNAKAN STOK'],
        ] as [$kode, $deskripsi, $tindakLanjut]) {
            DB::table('daftar_kebutuhan_materials')->insert([
                'id' => (string) Str::uuid(),
                'wbs' => self::WBS,
                'sheet_asal' => 'SHEET',
                'no' => '1',
                'rev' => 'A',
                'kode_material' => $kode,
                'kode_material_delete' => '',
                'deskripsi_material' => $deskripsi,
                'satuan' => 'PCS',
                'group' => 'KOMPONEN',
                'lot' => 'TS 1',
                'kebutuhan' => '1',
                'tindak_lanjut' => $tindakLanjut,
                'no_pr' => '',
                'qty_pr' => '1',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function test_export_applies_status_and_search_filters(): void
    {
        $content = $this->actingUser()
            ->get('/material-planning/export?proyek=' . urlencode(self::WBS) . '&statuses=SUDAH PR&search=BAIK')
            ->assertOk()
            ->streamedContent();

        $this->assertStringContainsString('MAT-1', $content);
        $this->assertStringNotContainsString('MAT-2', $content);
        $this->assertStringNotContainsString('MAT-3', $content);
    }

    public function test_export_without_extra_filters_downloads_all_rows(): void
    {
        $content = $this->actingUser()
            ->get('/material-planning/export?proyek=' . urlencode(self::WBS))
            ->assertOk()
            ->streamedContent();

        $this->assertStringContainsString('MAT-1', $content);
        $this->assertStringContainsString('MAT-2', $content);
        $this->assertStringContainsString('MAT-3', $content);
    }
}
