<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create roles if they don't exist
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $staffRole = Role::firstOrCreate(['name' => 'staff']);
        $viewer = Role::firstOrCreate(['name' => 'viewer']);

        // Create Admin User
        $adminPpc = User::firstOrCreate(
            ['email' => 'hadi.wuriyanto@inka.co.id'],
            [
                'name' => 'admin ppp',
                'nip' => '991200017',
                'password' => Hash::make('123456789'),
            ]
        );
        $adminPpc->assignRole($adminRole);
        $adminDmtp = User::firstOrCreate(
            ['email' => 'dedy.kurniawan@inka.co.id'],
            [
                'name' => 'admin dmtp bwi',
                'nip' => '991700045',
                'password' => Hash::make('123456789'),
            ]
        );
        $adminDmtp->assignRole($adminRole);
        $adminIt = User::firstOrCreate(
            ['email' => 'it@gmail.com'],
            [
                'name' => 'admin it',
                'nip' => '123456789',
                'password' => Hash::make('123456789'),
            ]
        );
        $adminIt->assignRole($adminRole);
    }
}
