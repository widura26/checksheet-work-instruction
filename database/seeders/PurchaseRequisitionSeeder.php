<?php

namespace Database\Seeders;

use App\Models\PurchaseRequisition;
use App\Models\User;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str; // Add this line

class PurchaseRequisitionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        // Pastikan ada user yang tersedia
        $users = User::all();
        if ($users->isEmpty()) {
            $this->command->info('No users found. Please run UserSeeder first.');

            return;
        }

        // Buat 10 Purchase Requisition
        for ($i = 1; $i <= 10; $i++) {
            $tanggalPr = $faker->dateTimeBetween('-6 months', 'now');

            PurchaseRequisition::create([
                'user_id' => $users->random()->id,
                'public_id' => Str::uuid(), // Add this line
                'no_pr' => 'PR-'.date('Y', $tanggalPr->getTimestamp()).'-'.str_pad($i, 4, '0', STR_PAD_LEFT),
                'tanggal_pr' => $tanggalPr->format('Y-m-d'),
                'status_pr' => $faker->randomElement(['sudah_datang', 'belum_datang', 'sudah_di_ct']),
            ]);
        }

        $this->command->info('PurchaseRequisition seeder completed successfully.');
    }
}
