<?php

namespace Database\Seeders;

use App\Models\Bagian;
use App\Models\Departemen;
use App\Models\DistribusiTool;
use App\Models\Project;
use App\Models\Unit;
use App\Models\User;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DistribusiToolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        $units = Unit::all();
        $bagians = Bagian::all();
        $departemens = Departemen::all();
        $users = User::all();
        $project = Project::all();
        if ($units->isEmpty() || $bagians->isEmpty() || $departemens->isEmpty()) {
            $this->command->info('Please run BagianSeeder, DepartemenSeeder, and UnitSeeder first.');

            return;
        }

        $types = ['peminjaman', 'penyerahan'];
        $statuses = ['kembali', 'kembali_sebagian', 'diserahkan', 'belum_kembali'];

        for ($i = 0; $i < 50; $i++) {
            $selectedType = $faker->randomElement($types);
            if ($selectedType === 'penyerahan') {
                $selectedStatus = 'diserahkan';
            } else {
                $selectedStatus = $faker->randomElement($statuses);
            }
            $tanggalPinjam = $faker->dateTimeBetween('-1 year', 'now');
            $tanggalKembali = null;

            if ($selectedStatus === 'kembali' || $selectedStatus === 'kembali_sebagian') {
                $tanggalKembali = $faker->dateTimeBetween($tanggalPinjam, 'now');
            }

            $randomUser = $users->random();

            DistribusiTool::create([
                'public_id' => Str::uuid(),
                'user_id' => $randomUser->id,
                'nama_peminjam' => $randomUser->name, // Using user name for peminjam
                'nip' => $randomUser->nip, // Using user nip
                'unit_id' => $units->random()->id,
                'bagian_id' => $bagians->random()->id,
                'departemen_id' => $departemens->random()->id,
                'project_id' => $project->random()->project_id,
                'type' => $selectedType,
                'status' => $selectedStatus,
                'tanggal_kembali' => $tanggalKembali,
                'tanggal_pinjam_diserahkan' => $tanggalPinjam,
            ]);
        }
    }
}
