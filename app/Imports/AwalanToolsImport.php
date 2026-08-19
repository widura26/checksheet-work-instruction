<?php

namespace App\Imports;

use App\Models\DaftarKodeMaterial;
use App\Models\Project;
use App\Models\Tools;
use App\Models\UpdateStockTool;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class AwalanToolsImport implements ToCollection, WithHeadingRow, WithValidation, SkipsEmptyRows
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function collection(Collection $rows)
    {
        try {
            DB::beginTransaction();
            foreach ($rows as $row) {
                $totalRow = (float) $row['total'];
                $posisiCtId = null;
                $kodeMaterialExist = DaftarKodeMaterial::where('kode_material', trim($row['kode_material']))->first();
                if (!$kodeMaterialExist) {
                    throw new \Exception('Kode material ' . $row['kode_material'] . ' tidak ditemukan.');
                }
                if (trim($row['posisi_ct']) == 'FAB') {
                    $posisiCtId = 1;
                } else if (trim($row['posisi_ct']) == 'FIN') {
                    $posisiCtId = 2;
                } else if (trim($row['posisi_ct']) == 'UU') {
                    $posisiCtId = 3;
                } else {
                    throw new \Exception('Posisi CT ' . $row['posisi_ct'] . ' tidak ditemukan.');
                }
                $projectId = trim($row['project_id']);
                $projectExist = DB::table('projects')->where('project_id', trim($row['project_id']))->first();
                if (!$projectExist) {
                    throw new \Exception('Project ' . $projectId . ' tidak ditemukan.');
                }
                $toolsExist = Tools::where('kode_material', $kodeMaterialExist->kode_material)
                    ->where('posisi_ct_id', $posisiCtId)
                    ->first();
                if ($toolsExist) {
                    $projectStock = DB::table('tool_project_stocks')
                        ->where('tool_id', $toolsExist->id)
                        ->where('posisi_ct_id', $posisiCtId)
                        ->where('project_id', $projectExist->project_id)
                        ->first();
                    if ($projectStock) {
                        DB::table('tool_project_stocks')
                            ->where('id', $projectStock->id)
                            ->update([
                                'qty' => $projectStock->qty + $totalRow,
                                'updated_at' => now(),
                            ]);
                    } else {
                        DB::table('tool_project_stocks')->insert([
                            'tool_id' => $toolsExist->id,
                            'posisi_ct_id' => $posisiCtId,
                            'project_id' => $projectExist->project_id,
                            'qty' => $totalRow,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                } else {
                    $toolCreate = Tools::create([
                        'public_id' => Str::uuid(),
                        'kode_material' => $kodeMaterialExist->kode_material,
                        'deskripsi' => $kodeMaterialExist->deskripsi,
                        'spesifikasi' => $kodeMaterialExist->spesifikasi,
                        'satuan' => $kodeMaterialExist->satuan,
                        'vendor' => trim($row['vendor']),
                        'keterangan_vendor' => trim($row['keterangan_vendor']),
                        'jenis_material' => trim(strtolower($row['kategori'])),
                        'tanggal_ditambahkan' => now(),
                        'posisi_ct_id' => $posisiCtId,
                    ]);
                    DB::table('tool_project_stocks')->insert([
                        'tool_id' => $toolCreate->id,
                        'posisi_ct_id' => $posisiCtId,
                        'project_id' => $projectExist->project_id,
                        'qty' => $totalRow,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('Error import tools: ' . $th->getMessage());
        }
    }
    public function rules(): array
    {
        return [
            'kode_material' => 'required|exists:daftar_kode_materials,kode_material',
        ];
    }
}
