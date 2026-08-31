<?php

namespace Database\Seeders;

use App\Models\Status;
use Illuminate\Database\Seeder;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            [
                'name' => 'Barcode',
                'description' => 'Cutting plan diproses untuk dibarcode'
            ],
            [
                'name' => 'Dikirim ke Renmat',
                'description' => 'Pengiriman cutting plan ke perencanaan material'
            ],
            [
                'name' => 'Dikirim ke Dalmat',
                'description' => 'Pengiriman cutting plan ke pengendalian material'
            ],
            [
                'name' => 'Proses BPM',
                'description' => 'Proses BPM oleh dalmat'
            ],
        ];

        foreach ($statuses as $status) {
            Status::firstOrCreate(
                ['name' => $status['name']],
                ['description' => $status['description']]
            );
        }
    }
}
