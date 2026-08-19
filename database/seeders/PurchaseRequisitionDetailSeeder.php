<?php

namespace Database\Seeders;

use App\Models\DetailKerusakanTool;
use App\Models\PurchaseRequisition;
use App\Models\PurchaseRequisitionDetail;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str; // Add this line

class PurchaseRequisitionDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        // Pastikan ada PurchaseRequisition yang tersedia
        $purchaseRequisitions = PurchaseRequisition::all();
        if ($purchaseRequisitions->isEmpty()) {
            $this->command->info('No PurchaseRequisition found. Please run PurchaseRequisitionSeeder first.');

            return;
        }

        // Pastikan ada DetailKerusakanTool yang tersedia dan belum digunakan
        $availableDetailKerusakanTools = DetailKerusakanTool::whereNotIn('id', function ($query) {
            $query->select('detail_kerusakan_tools_id')
                ->from('purchase_requisition_details');
        })->get();

        if ($availableDetailKerusakanTools->isEmpty()) {
            $this->command->info('No available DetailKerusakanTool found. All tools may already be assigned to PR or DetailKerusakanToolSeeder needs to be run first.');

            return;
        }

        $usedDetailKerusakanToolIds = [];

        // Untuk setiap Purchase Requisition, buat 1-3 detail
        foreach ($purchaseRequisitions as $pr) {
            $detailCount = $faker->numberBetween(1, 3);

            for ($i = 0; $i < $detailCount; $i++) {
                // Ambil DetailKerusakanTool yang belum digunakan
                $availableTools = $availableDetailKerusakanTools->whereNotIn('id', $usedDetailKerusakanToolIds);

                if ($availableTools->isEmpty()) {
                    $this->command->info('No more available DetailKerusakanTool for PR ID: '.$pr->id);
                    break;
                }

                $selectedTool = $availableTools->random();
                $usedDetailKerusakanToolIds[] = $selectedTool->id;

                PurchaseRequisitionDetail::create([
                    'public_id' => Str::uuid(), // Add this line
                    'purchase_requisition_id' => $pr->id,
                    'detail_kerusakan_tools_id' => $selectedTool->id,
                ]);
            }
        }

        $this->command->info('PurchaseRequisitionDetail seeder completed successfully.');
    }
}
