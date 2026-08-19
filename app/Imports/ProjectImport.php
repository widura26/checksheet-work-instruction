<?php

namespace App\Imports;

use App\Models\Project;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;

class ProjectImport implements ToCollection, WithHeadingRow, SkipsEmptyRows
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            Project::insertOrIgnore([
                'project_id' => trim($row['wbs_elem']),
                'nama_project' => $row['name'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
