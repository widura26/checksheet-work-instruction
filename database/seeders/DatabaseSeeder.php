<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Database\Seeders\UpdateStockToolSeeder as SeedersUpdateStockToolSeeder;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Facades\Activity;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Activity::disableLogging();
        $this->call([
            UserSeeder::class,
            // ProjectSeeder::class,
            DepartemenSeeder::class,
            // Posisi_CTSeeder::class,
            // ToolSeeder::class,
            // DaftarKodeMaterialSeeder::class, // Skipped
            // ToolStockBebasSeeder::class,
            // SeedersUpdateStockToolSeeder::class,
            // DistribusiToolSeeder::class,
            // DaftarDistribusiToolSeeder::class,
            // DetailDaftarDistribusiToolSeeder::class,
            // DetailKerusakanToolSeeder::class,
            // PenguranganToolSeeder::class,
            // PurchaseRequisitionSeeder::class,
            // PurchaseRequisitionDetailSeeder::class,
            // UpdateStockToolProjectSeeder::class,
            // MaterialTransferPostingSeeder::class,
            // MaterialTransferHistoryItemSeeder::class,
        ]);
        DB::table('kode_bagian_spm')->insert([
            'kode' => 4311,
            'nama_manager' => 'HADI WURIYANTO',
            'jabatan' => 'M. RENPROD & DALMAT BWI',
            'stok_rendah' => 10
        ]);
        Activity::enableLogging();
    }
}
