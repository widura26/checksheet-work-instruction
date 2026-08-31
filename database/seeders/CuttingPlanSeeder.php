<?php

namespace Database\Seeders;

use App\Models\CuttingPlan;
use App\Models\Part;
use App\Models\Project;
use App\Models\Proses;
use App\Models\Material;
use App\Models\User;
use Illuminate\Database\Seeder;

class CuttingPlanSeeder extends Seeder
{
    public function run(): void
    {
        $userId = User::query()->orderBy('id')->value('id');
        $projectId = Project::query()->orderBy('id')->value('id');
        $processId = Proses::query()->orderBy('id')->value('id');
        $materialId = Material::query()->orderBy('id')->value('id');

        $plans = [
            [
                'no_cp' => 'CP-2026-0001',
                'material_id' => $materialId,
                'wbs' => 'WBS-CP-001',
                'material_size' => 'PL 10 x 2000 x 6000',
                'quantity' => 12,
                'qty_lot' => 4,
                'special_instruction' => 'Toleransi potong +/- 0.5 mm.',
                'date_issued' => '2026-01-20',
                'date_revised' => '2026-02-01',
                'parts' => [
                    [
                        'part_number' => 'PN-0001-A',
                        'cutting_size' => '200 x 300',
                        'quantity_per_car' => 2,
                        'quantity_per_lot' => 8,
                        'part_name' => 'Side Frame Kiri',
                        'remark' => 'Bogie frame',
                    ],
                    [
                        'part_number' => 'PN-0001-B',
                        'cutting_size' => '250 x 320',
                        'quantity_per_car' => 2,
                        'quantity_per_lot' => 8,
                        'part_name' => 'Side Frame Kanan',
                        'remark' => 'Bogie frame',
                    ],
                    [
                        'part_number' => 'PN-0001-C',
                        'cutting_size' => '150 x 150',
                        'quantity_per_car' => 1,
                        'quantity_per_lot' => 4,
                        'part_name' => 'Cross Bracket',
                        'remark' => null,
                    ],
                ],
            ],
            [
                'no_cp' => 'CP-2026-0002',
                'material_id' => $materialId,
                'wbs' => 'WBS-CP-002',
                'material_size' => 'PL 8 x 1500 x 6000',
                'quantity' => 20,
                'qty_lot' => 5,
                'special_instruction' => 'Permukaan halus, hindari burn mark.',
                'date_issued' => '2026-03-10',
                'date_revised' => null,
                'parts' => [
                    [
                        'part_number' => 'PN-0002-A',
                        'cutting_size' => '300 x 400',
                        'quantity_per_car' => 4,
                        'quantity_per_lot' => 20,
                        'part_name' => 'Body Panel Depan',
                        'remark' => 'Bus listrik 8m',
                    ],
                    [
                        'part_number' => 'PN-0002-B',
                        'cutting_size' => '300 x 400',
                        'quantity_per_car' => 4,
                        'quantity_per_lot' => 20,
                        'part_name' => 'Body Panel Belakang',
                        'remark' => 'Bus listrik 8m',
                    ],
                ],
            ],
            [
                'no_cp' => 'CP-2026-0003',
                'material_id' => $materialId,
                'wbs' => 'WBS-CP-003',
                'material_size' => 'PL 12 x 2000 x 6000',
                'quantity' => 8,
                'qty_lot' => 4,
                'special_instruction' => null,
                'date_issued' => '2026-04-15',
                'date_revised' => null,
                'parts' => [
                    [
                        'part_number' => 'PN-0003-A',
                        'cutting_size' => '180 x 180',
                        'quantity_per_car' => 2,
                        'quantity_per_lot' => 8,
                        'part_name' => 'Bracket Suspensi',
                        'remark' => null,
                    ],
                ],
            ],
        ];

        foreach ($plans as $planData) {
            $parts = $planData['parts'];
            unset($planData['parts']);

            $cuttingPlan = CuttingPlan::updateOrCreate(
                ['no_cp' => $planData['no_cp']],
                array_merge($planData, [
                    'project_id' => $projectId,
                    'process_id' => $processId,
                    'prepared_by' => $userId,
                    'approved_by' => $userId,
                    'date_operator' => $userId,
                ])
            );

            foreach ($parts as $part) {
                Part::updateOrCreate(
                    [
                        'cutting_plan_id' => $cuttingPlan->id,
                        'part_number' => $part['part_number'],
                    ],
                    $part
                );
            }
        }
    }
}
