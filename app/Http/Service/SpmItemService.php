<?php

namespace App\Http\Service;

use App\Models\SpmItem;
use App\Models\Tools;
use Exception;

class SpmItemService
{
    public function saveItems($data, $posisi_ct_id, $project_id, $spmId)
    {
        try {
            foreach ($data as $key => $item) {
                $tool = Tools::where('kode_material', $item['kode_material'])
                    ->whereHas('posisiCT', fn($q) => $q->where('id', $posisi_ct_id))
                    ->first();
                if (! $tool) {
                    // if data with that kode_material not found in tools table
                    SpmItem::create([
                        'spm_id' => $spmId,
                        'kode_material' => $item['kode_material'],
                        'deskripsi' => $item['deskripsi'],
                        'spesifikasi' => $item['spesifikasi'],
                        'requested_qty' => $item['requested_qty'],
                        'keterangan' => $item['keterangan'],
                        'kategori' => $item['kategori'],
                        'satuan' => $item['satuan'],
                    ]);
                } else {
                    SpmItem::create([
                        'spm_id' => $spmId,
                        'tool_id' => $tool->id,
                        'kode_material' => $item['kode_material'],
                        'deskripsi' => $item['deskripsi'],
                        'spesifikasi' => $item['spesifikasi'],
                        'requested_qty' => $item['requested_qty'],
                        'keterangan' => $item['keterangan'],
                        'kategori' => $item['kategori'],
                        'satuan' => $item['satuan'],
                    ]);
                }
            }
        } catch (\Throwable $th) {
            throw new Exception($th->getMessage());
        }
    }
}
