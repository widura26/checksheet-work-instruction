<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class BagianSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SqlDumpImporter::import('bagians', 'bagians.sql');
    }
}
