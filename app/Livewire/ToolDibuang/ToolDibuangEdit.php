<?php

namespace App\Livewire\ToolDibuang;

use App\Models\DaftarToolDibuang;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class ToolDibuangEdit extends Component
{
    public $noBs;
    public $toolDibuangId;
    public $daftarToolDibuang;
    public $deletedIds = [];
    public function mount($daftarToolDibuang, $noBs, $toolDibuangId)
    {
        $this->daftarToolDibuang = $daftarToolDibuang->toArray();
        $this->noBs = $noBs;
        $this->toolDibuangId = $toolDibuangId;
    }
    public function addRow()
    {
        $this->daftarToolDibuang[] = [
            'id' => null,
            'tool_dibuang_id' => $this->toolDibuangId,
            'kode_material' => '',
            'deskripsi' => '',
            'spesifikasi' => '',
            'detail_kerusakan' => '',
        ];
    }
    public function delete($index)
    {
        $this->deletedIds[] = $this->daftarToolDibuang[$index]['id'];
        unset($this->daftarToolDibuang[$index]);
        $this->daftarToolDibuang = array_values($this->daftarToolDibuang);
    }
    public function save()
    {
        try {
            DB::beginTransaction();
            $data = collect($this->daftarToolDibuang)->map(function ($item) {
                return [
                    'id' => $item['id'],
                    'tool_dibuang_id' => $item['tool_dibuang_id'],
                    'kode_material' => $item['kode_material'],
                    'deskripsi' => $item['deskripsi'],
                    'spesifikasi' => $item['spesifikasi'],
                    'detail_kerusakan' => $item['detail_kerusakan'],
                    'updated_at' => now(),
                ];
            })->toArray();
            DaftarToolDibuang::upsert($data, ['id'], [
                'kode_material',
                'deskripsi',
                'spesifikasi',
                'detail_kerusakan',
                'updated_at'
            ]);
            DaftarToolDibuang::whereIn('id', $this->deletedIds)->delete();
            DB::commit();
            return redirect()->route('tool-dibuang.index')->with('success', 'Data berhasil update!');
        } catch (\Exception $e) {
            DB::rollback();
            dd($e->getMessage());
            Log::error('Error saat menyimpan data Tool Dibuang: ' . $e->getMessage());
            return redirect()->route('tool-dibuang.index')->with('error', 'Terjadi kesalahan saat menyimpan data: ');
        }
    }
    public function render()
    {
        return view('livewire.tool-dibuang.tool-dibuang-edit');
    }
}
