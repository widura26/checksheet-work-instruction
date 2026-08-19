<?php

namespace Database\Seeders;

use App\Models\MaterialTransferHistory;
use App\Models\MaterialTransferHistoryItem;
use App\Models\Tools;
use App\Models\User;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class MaterialTransferHistoryItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create('id_ID');
        $materialTransferHistories = MaterialTransferHistory::all();
        $tools = Tools::all();
        $users = User::all();

        if ($materialTransferHistories->isEmpty() || $tools->isEmpty() || $users->isEmpty()) {
            $this->command->info('Please run MaterialTransferPostingSeeder, ToolSeeder, and UserSeeder first.');

            return;
        }

        foreach ($materialTransferHistories as $history) {
            // For each history, create 1 to 3 items
            $numberOfItems = $faker->numberBetween(1, 3);
            for ($i = 0; $i < $numberOfItems; $i++) {
                MaterialTransferHistoryItem::create([
                    'material_transfer_history_id' => $history->id,
                    'tool_id' => $tools->random()->id,
                    'qty_returned' => $faker->numberBetween(1, 10),
                    'user_id' => $users->random()->id, // Assuming user_id for the item
                ]);
            }
        }
    }
}
