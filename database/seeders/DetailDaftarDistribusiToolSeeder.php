<?php

namespace Database\Seeders;

use App\Models\DaftarDistribusiTool;
use App\Models\DetailDaftarDistribusiTool;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class DetailDaftarDistribusiToolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        $daftarDistribusiTools = DaftarDistribusiTool::all();

        if ($daftarDistribusiTools->isEmpty()) {
            $this->command->info('Please run DaftarDistribusiToolSeeder first.');

            return;
        }

        foreach ($daftarDistribusiTools as $daftarDistribusiTool) {
            // Only create detail if the item was returned (partially or fully)
            if ($daftarDistribusiTool->qty_kembali > 0) {
                // If status is 'kembali_sebagian', create multiple entries for different conditions
                if ($daftarDistribusiTool->status === 'kembali_sebagian') {
                    $qtyRusak = $faker->numberBetween(1, $daftarDistribusiTool->qty_kembali);
                    $qtyBaik = $daftarDistribusiTool->qty_kembali - $qtyRusak;

                    if ($qtyBaik > 0) {
                        DetailDaftarDistribusiTool::create([
                            'daftar_distribusi_tool_id' => $daftarDistribusiTool->id,
                            'jumlah' => $qtyBaik,
                            'kondisi' => 'baik',
                            'tanggal_kembali' => $daftarDistribusiTool->tanggal_kembali, // Add this line
                        ]);
                    }
                    if ($qtyRusak > 0) {
                        DetailDaftarDistribusiTool::create([
                            'daftar_distribusi_tool_id' => $daftarDistribusiTool->id,
                            'jumlah' => $qtyRusak,
                            'kondisi' => $faker->randomElement(['rusak ringan', 'rusak berat']),
                            'tanggal_kembali' => $daftarDistribusiTool->tanggal_kembali, // Add this line
                        ]);
                    }
                } else { // 'kembali'
                    DetailDaftarDistribusiTool::create([
                        'daftar_distribusi_tool_id' => $daftarDistribusiTool->id,
                        'jumlah' => $daftarDistribusiTool->qty_kembali,
                        'kondisi' => $faker->randomElement(['baik', 'rusak ringan', 'rusak berat']),
                        'tanggal_kembali' => $daftarDistribusiTool->tanggal_kembali, // Add this line
                    ]);
                }
            }
        }
    }
}
