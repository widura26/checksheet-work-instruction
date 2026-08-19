<?php

namespace Database\Seeders;

// Corrected casing
use App\Models\Project;
use App\Models\ToolProjectStock;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MaterialTransferPostingSeeder extends Seeder // Renamed class
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::reconnect(); // Reconnect to ensure fresh view of database schema

        $users = User::all();
        $projects = Project::all();

        if ($users->isEmpty() || $projects->count() < 2) {
            $this->command->info('Requires at least 1 user and 2 projects to run MaterialTransferPostingSeeder.');

            return;
        }

        // Find a stock entry that is suitable for a transfer
        $sourceStock = ToolProjectStock::where('qty', '>=', 5)->first();
        if (! $sourceStock) {
            $this->command->info('No project stock found with sufficient quantity (>= 5) to seed a transfer.');

            return;
        }

        $sourceProject = Project::where('project_id', $sourceStock->project_id)->first();
        $destinationProject = Project::where('project_id', '!=', $sourceStock->project_id)->first();
        $user = $users->random();
        $transferQty = rand(1, 4);

        if (! $sourceProject || ! $destinationProject) {
            $this->command->info('Could not find suitable source or destination project.');

            return;
        }

        $this->command->info("Seeding transfer from project {$sourceProject->project_id} to {$destinationProject->project_id}.");

        try {
            DB::beginTransaction();

            $postingData = [
                'transfer_code' => Str::uuid(),
                'from_project_id' => $sourceProject->project_id,
                'to_project_id' => $destinationProject->project_id,
                'transfer_date' => now()->subDays(rand(1, 30)),
                'status' => 'ON_LOAN',
                'created_by' => $user->id,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Use DB::table()->insertGetId() to get the ID of the new record
            $postingId = DB::table('material_transfer_postings')->insertGetId($postingData);

            // Create items directly via DB::table
            DB::table('material_transfer_posting_items')->insert([
                'material_transfer_posting_id' => $postingId,
                'tool_id' => $sourceStock->tool_id,
                'posisi_ct_id' => $sourceStock->posisi_ct_id,
                'from_project_id' => $sourceProject->project_id,
                'qty' => $transferQty,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Decrement stock from source (ToolProjectStock is an Eloquent model, should be fine)
            $sourceStock->decrement('qty', $transferQty);

            // Increment stock in destination (ToolProjectStock is an Eloquent model, should be fine)
            $destinationStock = ToolProjectStock::firstOrNew([
                'project_id' => $destinationProject->project_id,
                'tool_id' => $sourceStock->tool_id,
                'posisi_ct_id' => $sourceStock->posisi_ct_id,
            ]);
            $destinationStock->increment('qty', $transferQty);

            // Create history directly via DB::table
            DB::table('material_transfer_histories')->insert([
                'material_transfer_posting_id' => $postingId,
                'action' => 'CREATED',
                'notes' => 'Seed-generated transfer from '.$sourceProject->project_id.' to '.$destinationProject->project_id,
                'user_id' => $user->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();
            $this->command->info('MaterialTransferPostingSeeder completed successfully.');
        } catch (\Throwable $th) {
            DB::rollBack();
            $this->command->error('An error occurred during MaterialTransferPostingSeeder: '.$th->getMessage());
        }
    }
}
