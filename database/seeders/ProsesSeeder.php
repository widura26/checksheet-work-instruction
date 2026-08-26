<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ProsesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SqlDumpImporter::import('processes', 'processes.sql');
    }
}
