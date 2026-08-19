<?php

namespace App\Livewire\PenggantianTool;

use App\Models\Bagian;
use App\Models\Departemen;
use App\Models\DistribusiTool;
use App\Models\Unit;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Illuminate\Support\Facades\Storage;
use App\Models\PenggantianTool;
use Illuminate\Validation\ValidationException;
use Livewire\WithFileUploads;

class Create extends Component
{
    use WithFileUploads;
    public $departemenId;
    public $bagianId;
    public $unitId;
    public $departemens;
    public $bagians = [];
    public $distribusi = [];
    public $units = [];
    public $showModal = false;
    public $toolId;
    public $qtyKerusakan;
    public $fileBukti;
    public $keterangan = null;
    public $totalDistribusi;
    public function mount()
    {
        $this->departemens = Departemen::all();
    }

    public function updatedDepartemenId($value)
    {
        $this->bagians = Bagian::where('departemen_id', $value)->get();
        $this->reset('bagianId', 'unitId');
    }

    public function updatedBagianId($value)
    {
        $this->units = Unit::where('bagian_id', $value)->get();
        $this->reset('unitId');
    }

    public function penggantian($toolId, $totalDistribusi)
    {
        $this->toolId = $toolId;
        $this->totalDistribusi = $totalDistribusi;
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset('qtyKerusakan', 'fileBukti', 'keterangan', 'showModal');
    }

    public function save()
    {

        $validated = $this->validate([
            'qtyKerusakan' => 'required|integer|min:1|lte:' . $this->totalDistribusi,
            'fileBukti' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);
        try {
            DB::transaction(function () use ($validated) {
                $filePath = $this->fileBukti->store('bukti-penggantian', 'public');
                PenggantianTool::create([
                    'tool_id' => $this->toolId,
                    'qty_diganti' => $validated['qtyKerusakan'],
                    'departemen_id' => $this->departemenId,
                    'bagian_id' => $this->bagianId,
                    'unit_id' => $this->unitId,
                    'tanggal_penggantian' => now(),
                    'status' => 'open',
                    'keterangan' => $this->keterangan,
                    'file' => $filePath,
                ]);
            });
            session()->flash('success', 'Data penggantian berhasil disimpan.');
            $this->closeModal();
        } catch (\Throwable $e) {
            session()->flash('error', 'Terjadi kesalahan');
            $this->closeModal();
        }
    }

    public function render()
    {
        if ($this->departemenId && $this->bagianId && $this->unitId) {
            $this->distribusi = DistribusiTool::query()
                ->join('daftar_distribusi_tools', 'distribusi_tools.id', '=', 'daftar_distribusi_tools.distribusi_tools_id')
                ->join('tools', 'daftar_distribusi_tools.tool_id', '=', 'tools.id')
                ->join('posisi__c_t_s', 'daftar_distribusi_tools.posisi_ct_id', '=', 'posisi__c_t_s.id')
                ->where('distribusi_tools.type', 'penyerahan')
                ->where('distribusi_tools.departemen_id', $this->departemenId)
                ->where('distribusi_tools.bagian_id', $this->bagianId)
                ->where('distribusi_tools.unit_id', $this->unitId)
                ->where('tools.jenis_material', 'tool')
                ->select(
                    'tools.id as tool_id',
                    'tools.deskripsi',
                    'tools.kode_material',
                    DB::raw('SUM(daftar_distribusi_tools.jumlah_distribusi) as total_distribusi'),
                    DB::raw('GROUP_CONCAT(daftar_distribusi_tools.id) as distribusi_ids')
                )
                ->groupBy('tools.deskripsi', 'tools.kode_material', 'tools.id')
                ->get();
        }

        return view('livewire.penggantian-tool.create', [
            'distribusi' => $this->distribusi,
        ]);
    }
}
