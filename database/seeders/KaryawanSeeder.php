<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class KaryawanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SqlDumpImporter::import('daftar_karyawans', 'daftar_karyawans.sql');
    }
}
