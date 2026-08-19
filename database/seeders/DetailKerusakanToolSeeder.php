<?php

namespace Database\Seeders;

use App\Models\DetailDaftarDistribusiTool;
use App\Models\DetailKerusakanTool;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class DetailKerusakanToolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        $damagedDetails = DetailDaftarDistribusiTool::all();

        if ($damagedDetails->isEmpty()) {
            $this->command->info('No damaged DetailDaftarDistribusiTool entries found. Please ensure DaftarDistribusiToolSeeder and DetailDaftarDistribusiToolSeeder are run first and generate damaged items.');

            return;
        }

        foreach ($damagedDetails as $detail) {
            $statusPengajuanSpr = $faker->randomElement(['sudah', 'belum']);
            $statusPerbaikanOptions = ['menunggu_spr', 'sedang_diperbaiki', 'selesai', 'tidak_bisa_diperbaiki'];

            $noSpr = null;
            $tanggalSpr = null;
            $fileSpr = null;
            $statusPerbaikan = 'menunggu_spr'; // Default

            if ($statusPengajuanSpr === 'sudah') {
                $noSpr = 'SPR-'.$faker->randomNumber(5, true);
                $tanggalSpr = $faker->date();
                $fileSpr = 'documents/spr/'.$faker->uuid().'.pdf';
                $statusPerbaikan = $faker->randomElement(array_diff($statusPerbaikanOptions, ['menunggu_spr'])); // Can be any status except 'menunggu_spr'
            } else {
                $statusPerbaikan = 'menunggu_spr'; // If SPR not submitted, it's still waiting
            }

            DetailKerusakanTool::create([
                'detail_daftar_distribusi_tools_id' => $detail->id,
                'deskripsi_kerusakan' => $faker->paragraph(2),
                'status_pengajuan_spr' => $statusPengajuanSpr,
                'no_spr' => $noSpr,
                'tanggal_spr' => $tanggalSpr,
                'file_spr' => $fileSpr,
                'status_perbaikan' => $statusPerbaikan,
                'public_id' => $faker->uuid(),
                'keterangan' => $faker->randomElement(['sudah_diganti', 'belum_diganti']), // Add this line
            ]);
        }
    }
}
