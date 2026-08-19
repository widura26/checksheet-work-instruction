<?php

namespace Database\Seeders;

use App\Models\Posisi_CT;
use Illuminate\Database\Seeder;

class Posisi_CTSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Posisi_CT::create(['nama_posisi_ct' => 'CT_Fabrikasi']);
        Posisi_CT::create(['nama_posisi_ct' => 'CT_Finishing']);
    }
}
