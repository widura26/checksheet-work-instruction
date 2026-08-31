<?php

namespace Database\Seeders;

use App\Models\Mdr;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ManufactureDrawingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userId = User::query()->orderBy('id')->value('id');

        $projects = [
            '1d13eb56-44fb-4629-9bfc-39cf124bb521',
            '4e74ed78-0840-43cd-a5e7-693f8b6c96a2',
            '40429c63-23b4-4925-889a-57a8ac8a34ff',
            '50449104-fe4f-4846-b505-7c1079659902',
            '09eb5937-84e5-45f9-bbfe-c83ea86e30b8',
        ];

        $drawings = [
            [
                'manufacture_drawing_code' => 'MD-2026-0001',
                'project_id' => $projects[0],
                'manufacture_drawing_date' => '2026-01-15',
                'description' => 'Gambar manufaktur bogie untuk Kereta MSA LRT Palembang.',
            ],
            [
                'manufacture_drawing_code' => 'MD-2026-0002',
                'project_id' => $projects[1],
                'manufacture_drawing_date' => '2026-02-10',
                'description' => 'Gambar manufaktur bodi bus listrik ukuran 8 meter.',
            ],
            [
                'manufacture_drawing_code' => 'MD-2026-0003',
                'project_id' => $projects[2],
                'manufacture_drawing_date' => '2026-03-05',
                'description' => 'Gambar manufaktur bogie pembelian berulang 24 unit PT KAI.',
            ],
            [
                'manufacture_drawing_code' => 'MD-2026-0004',
                'project_id' => $projects[3],
                'manufacture_drawing_date' => '2026-03-20',
                'description' => 'Gambar manufaktur komponen sewa bus listrik DAMRI.',
            ],
            [
                'manufacture_drawing_code' => 'MD-2026-0005',
                'project_id' => $projects[4],
                'manufacture_drawing_date' => '2026-04-12',
                'description' => 'Gambar manufaktur wheelset reprofiling Freeport.',
            ],
            [
                'manufacture_drawing_code' => 'MD-2026-0006',
                'project_id' => null,
                'manufacture_drawing_date' => '2026-05-02',
                'description' => 'Gambar manufaktur umum tanpa proyek tertentu.',
            ],
        ];

        foreach ($drawings as $drawing) {
            Mdr::updateOrCreate(
                ['manufacture_drawing_code' => $drawing['manufacture_drawing_code']],
                array_merge($drawing, ['user_id' => $userId])
            );
        }
    }
}
