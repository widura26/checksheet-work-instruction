<?php

namespace App\Livewire\ToolDibuang;

use App\Imports\ToolDibuangImport;
use App\Models\DaftarToolDibuang;
use App\Models\ToolDibuang;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;

class ToolDibuangCreate extends Component
{
    use WithFileUploads;

    public $tanggal_dibuang;

    public $file_excel;

    public $daftarTool = [];

    public $importErrors = [];

    protected function rules()
    {
        return [
            'tanggal_dibuang' => 'required|date',
            'daftarTool' => 'required|array|min:1',
            'daftarTool.*.kode_material' => 'required|string|exists:daftar_kode_materials,kode_material',
            'daftarTool.*.deskripsi' => 'nullable|string',
            'daftarTool.*.spesifikasi' => 'nullable|string',
            'daftarTool.*.detail_kerusakan' => 'required|string',
        ];
    }

    protected function messages()
    {
        return [
            'tanggal_dibuang.required' => 'Tanggal dibuang harus diisi.',
            'daftarTool.required' => 'Minimal harus ada 1 item yang diimport.',
            'daftarTool.*.kode_material.required' => 'Kode material harus diisi.',
            'daftarTool.*.kode_material.exists' => 'Kode material tidak ditemukan.',
            'daftarTool.*.detail_kerusakan.required' => 'Detail kerusakan harus diisi.',
        ];
    }

    public function mount()
    {
        $this->tanggal_dibuang = now()->format('Y-m-d\TH:i');
    }

    public function updatedFileExcel()
    {
        $this->validate([
            'file_excel' => 'required|mimes:xlsx,xls,csv|max:4048',
        ], [
            'file_excel.required' => 'File Excel wajib diisi.',
            'file_excel.mimes' => 'Format file harus xlsx, xls, atau csv.',
            'file_excel.max' => 'Ukuran file maksimal 4MB.',
        ]);

        try {
            $import = new ToolDibuangImport;
            Excel::import($import, $this->file_excel->getRealPath());

            $this->importErrors = $import->errors;
            $this->daftarTool = $import->data;

            if (empty($this->importErrors)) {
                session()->flash('success_import', 'Silahkan cek kembali sebelum menyimpan.');
            } else {
                session()->flash('error_import', 'Terdapat kesalahan pada file yang diimport.');
                $this->daftarTool = [];
            }
        } catch (\Exception $e) {
            Log::error('Error importing tool dibuang: ' . $e->getMessage());
            session()->flash('error_import', 'Terjadi kesalahan saat memproses file: ' . $e->getMessage());
        }
    }

    public function save()
    {
        $this->validate();
        DB::beginTransaction();
        try {
            $toolDibuang = ToolDibuang::create([
                'nomor_bs' => $this->generateNomorBs(),
                'tanggal_dibuang' => $this->tanggal_dibuang,
            ]);
            $dataCreateToolDibuang = [];
            foreach ($this->daftarTool as $item) {
                $dataCreateToolDibuang[] = [
                    'tool_dibuang_id' => $toolDibuang->id,
                    'kode_material' => $item['kode_material'],
                    'deskripsi' => $item['deskripsi'],
                    'spesifikasi' => $item['spesifikasi'] ?? '-',
                    'detail_kerusakan' => $item['detail_kerusakan'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            DaftarToolDibuang::insert($dataCreateToolDibuang);
            DB::commit();
            session()->flash('success', 'Data tool dibuang berhasil disimpan.');
            return redirect()->route('tool-dibuang.index');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('error simpan tool dibuang: ' . $e->getMessage());
            session()->flash('error', 'Terjadi kesalahan saat menyimpan data.');
        }
    }

    private function generateNomorBs()
    {
        $year = date('Y');
        $kodeBagian = DB::table('kode_bagian_spm')->first();
        $lastToolDibuang = ToolDibuang::whereYear('created_at', $year)->count();
        $runningNumber = str_pad($lastToolDibuang + 1, 4, '0', STR_PAD_LEFT);

        return 'BS-' . $runningNumber . '/' . $kodeBagian->kode . '/INKA/' . $year;
    }

    public function render()
    {
        return view('livewire.tool-dibuang.tool-dibuang-create');
    }
}
