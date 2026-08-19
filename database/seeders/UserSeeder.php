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
        $adminPpc = User::create([
            'name' => 'admin ppp',
            'nip' => '991200017',
            'email' => 'hadi.wuriyanto@inka.co.id',
            'password' => Hash::make('123456789'),
        ]);
        $adminPpc->assignRole($adminRole);
        $adminDmtp = User::create([
            'name' => 'admin dmtp bwi',
            'nip' => '991700045',
            'email' => 'dedy.kurniawan@inka.co.id',
            'password' => Hash::make('123456789'),
        ]);
        $adminDmtp->assignRole($adminRole);
        $adminIt = User::create([
            'name' => 'admin it',
            'nip' => '123456789',
            'email' => 'it@gmail.com',
            'password' => Hash::make('123456789'),
        ]);
        $adminIt->assignRole($adminRole);
    }
}
