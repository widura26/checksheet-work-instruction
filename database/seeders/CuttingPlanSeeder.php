<?php

namespace Database\Seeders;

use App\Models\CuttingPlan;
use App\Models\CuttingPlanMaterial;
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
                'special_instruction' => 'Toleransi potong +/- 0.5 mm.',
                'date_issued' => '2026-01-20',
                'date_revised' => '2026-02-01',
                'materials' => [
                    [
                        'material_id' => $materialId,
                        'material_size' => 'PL 10 x 2000 x 6000',
                        'quantity' => 12,
                        'wbs' => 'WBS-CP-001',
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
                ],
            ],
            [
                'no_cp' => 'CP-2026-0002',
                'special_instruction' => 'Permukaan halus, hindari burn mark.',
                'date_issued' => '2026-03-10',
                'date_revised' => null,
                'materials' => [
                    [
                        'material_id' => $materialId,
                        'material_size' => 'PL 8 x 1500 x 6000',
                        'quantity' => 20,
                        'wbs' => 'WBS-CP-002',
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
                ],
            ],
            [
                'no_cp' => 'CP-2026-0003',
                'special_instruction' => null,
                'date_issued' => '2026-04-15',
                'date_revised' => null,
                'materials' => [
                    [
                        'material_id' => $materialId,
                        'material_size' => 'PL 12 x 2000 x 6000',
                        'quantity' => 8,
                        'wbs' => 'WBS-CP-003',
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
                ],
            ],
        ];

        foreach ($plans as $plan) {
            $materialsData = $plan['materials'];
            unset($plan['materials']);

            $cuttingPlan = CuttingPlan::updateOrCreate(
                ['no_cp' => $plan['no_cp']],
                array_merge($plan, [
                    'project_id' => $projectId,
                    'process_id' => $processId,
                    'prepared_by' => $userId,
                    'approved_by' => $userId,
                    'date_operator' => $userId,
                ])
            );

            // Delete existing materials and parts
            $cuttingPlan->materials()->each(function ($cpm) {
                $cpm->parts()->delete();
            });
            $cuttingPlan->materials()->delete();

            foreach ($materialsData as $mData) {
                $cpm = CuttingPlanMaterial::create([
                    'cutting_plan_id' => $cuttingPlan->id,
                    'material_id' => $mData['material_id'],
                    'material_size' => $mData['material_size'],
                    'quantity' => $mData['quantity'],
                    'wbs' => $mData['wbs'],
                ]);

                foreach ($mData['parts'] as $part) {
                    Part::create([
                        'cutting_plan_material_id' => $cpm->id,
                        'part_number' => $part['part_number'],
                        'cutting_size' => $part['cutting_size'],
                        'quantity_per_car' => $part['quantity_per_car'],
                        'quantity_per_lot' => $part['quantity_per_lot'],
                        'part_name' => $part['part_name'],
                        'remark' => $part['remark'],
                    ]);
                }
            }
        }
    }
}
