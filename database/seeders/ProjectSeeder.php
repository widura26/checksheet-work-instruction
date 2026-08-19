<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        Project::create([
            'project_id' => 'PROJ-123',
            'nama_project' => 'KCI-01',
        ]);
        Project::create([
            'project_id' => 'PROJ-007',
            'nama_project' => 'GD',
        ]);
    }
}
