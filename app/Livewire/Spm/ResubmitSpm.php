<?php

namespace App\Livewire\Spm;

use App\Http\Service\CreateSpmFileService;
use App\Http\Service\SpmItemService;
use App\Models\Bagian;
use App\Models\DaftarKodeMaterial;
use App\Models\Departemen;
use App\Models\Posisi_CT;
use App\Models\Project;
use App\Models\Spm;
use App\Models\SpmItem;
use App\Models\Tools;
use App\Models\Unit;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ResubmitSpm extends Component
{
    public Spm $spm;
    public $projects;
    public $posisiCts;
    public $project_id;
    public $posisi_ct_id;
    public $kepada;
    public $items = [];
    public $maxItems = 10;
    public $destinasi;
    public $departemens = [];
    public $bagians = [];
    public $units = [];
    public $departemen_id;
    public $bagian_id;
    public $unit_id;
    protected function rules()
    {
        $rules = [
            'project_id' => 'required|exists:projects,project_id',
            'destinasi' => 'required|in:ct,workshop',
            'posisi_ct_id' => 'required|exists:posisi__c_t_s,id',
            'kepada' => 'required|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.kode_material' => 'required|exists:daftar_kode_materials,kode_material',
            'items.*.deskripsi' => 'required|string',
            'items.*.spesifikasi' => 'nullable|string',
            'items.*.requested_qty' => 'required|integer|min:1',
            'items.*.keterangan' => 'nullable|string',
            'items.*.kategori' => 'required|string|in:tool,apd,consumable',
            'items.*.satuan' => 'required',
        ];
        if ($this->destinasi === 'workshop') {
            $rules = array_merge($rules, [
                'departemen_id' => 'required|exists:departemens,id',
                'bagian_id' => 'required|exists:bagians,id',
                'unit_id' => 'nullable|exists:units,id',
            ]);
        }

        return $rules;
    }

    protected function messages()
    {
        $messages = [
            'project_id.required' => 'Project wajib dipilih.',
            'project_id.exists' => 'Project yang dipilih tidak valid.',

            'destinasi.required' => 'Destinasi wajib dipilih.',
            'destinasi.in' => 'Destinasi harus CT atau Workshop.',

            'posisi_ct_id.required' => 'Posisi CT wajib dipilih.',
            'posisi_ct_id.exists' => 'Posisi CT tidak valid.',

            'kepada.required' => 'Tujuan penerima wajib diisi.',
            'kepada.max' => 'Tujuan penerima maksimal 255 karakter.',

            'items.required' => 'Minimal harus ada 1 item.',
            'items.array' => 'Format item tidak valid.',
            'items.min' => 'Minimal harus ada 1 item.',

            'items.*.kode_material.required' => 'Kode material wajib diisi.',
            'items.*.kode_material.exists' => 'Kode material tidak terdaftar.',

            'items.*.deskripsi.required' => 'Deskripsi item wajib diisi.',

            'items.*.requested_qty.required' => 'Jumlah permintaan wajib diisi.',
            'items.*.requested_qty.integer' => 'Jumlah permintaan harus berupa angka.',
            'items.*.requested_qty.min' => 'Jumlah permintaan minimal 1.',

            'items.*.kategori.required' => 'Kategori item wajib dipilih.',
            'items.*.kategori.in' => 'Kategori item harus Tool, APD, atau Consumable.',

            'items.*.satuan.required' => 'Satuan item wajib diisi.',
        ];

        if ($this->destinasi === 'workshop') {
            $messages = array_merge($messages, [
                'departemen_id.required' => 'Departemen wajib dipilih untuk destinasi Workshop.',
                'departemen_id.exists' => 'Departemen yang dipilih tidak valid.',

                'bagian_id.required' => 'Bagian wajib dipilih untuk destinasi Workshop.',
                'bagian_id.exists' => 'Bagian yang dipilih tidak valid.',

                'unit_id.exists' => 'Unit yang dipilih tidak valid.',
            ]);
        }

        return $messages;
    }

    public function mount(Spm $spm)
    {
        $this->spm = $spm;
        $this->projects = Project::orderBy('nama_project')->get();
        $this->posisiCts = Posisi_CT::orderBy('nama_posisi_ct')->get();
        $this->departemens = Departemen::orderBy('nama_departemen', 'asc')->get();

        $this->project_id = $this->spm->project_id;
        $this->posisi_ct_id = $this->spm->posisi_ct_id;
        $this->kepada = $this->spm->kepada;
        $this->destinasi = $this->spm->destinasi;
        $this->departemen_id = $this->spm->departemen_id;
        $this->bagian_id = $this->spm->bagian_id;
        $this->unit_id = $this->spm->unit_id;

        $this->items = [];
        foreach ($spm->items as $item) {
            $this->items[] = [
                'kode_material' => $item->kode_material,
                'deskripsi' => $item->deskripsi,
                'spesifikasi' => $item->spesifikasi,
                'requested_qty' => $item->requested_qty,
                'keterangan' => $item->keterangan,
                'kategori' => $item->kategori,
                'satuan' => $item->satuan,
                'error' => null,
            ];
        }

        if ($this->destinasi === 'workshop') {
            $this->bagians = Bagian::where('departemen_id', $this->departemen_id)->get();
            $this->units = Unit::where('bagian_id', $this->bagian_id)->get();
        } else {
            $this->bagians = collect();
            $this->units = collect();
        }

        if (empty($this->items)) {
            $this->items = [
                ['kode_material' => '', 'spesifikasi' => '', 'deskripsi' => '', 'requested_qty' => 1, 'keterangan' => '', 'kategori' => '', 'satuan' => '', 'error' => null],
            ];
        }

        $this->recheckAllDuplicateErrors();
    }

    public function updatedDepartemenId($value)
    {
        $this->bagians = Bagian::where('departemen_id', $value)->get();
        $this->reset('bagian_id', 'unit_id');
    }

    public function updatedBagianId($value)
    {
        $this->units = Unit::where('bagian_id', $value)->get();
        $this->reset('unit_id');
    }

    public function updatedDestinasi()
    {
        if ($this->destinasi !== 'workshop') {
            $this->reset(['departemen_id', 'bagian_id', 'unit_id']);
        }
    }

    public function addItem()
    {
        if (count($this->items) < $this->maxItems) {
            $this->items[] = ['kode_material' => '', 'spesifikasi' => '', 'deskripsi' => '', 'requested_qty' => 1, 'keterangan' => '', 'satuan' => '', 'kategori' => '', 'error' => null];
            $this->recheckAllDuplicateErrors();
        }
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
        $this->recheckAllDuplicateErrors();
    }

    public function updatedItems($value, $key)
    {
        $parts = explode('.', $key);
        $index = $parts[0];
        $field = $parts[1];

        if ($field === 'kode_material') {
            $this->items[$index]['deskripsi'] = '';
            $this->items[$index]['spesifikasi'] = '';
            $this->items[$index]['error'] = null;

            if (! empty($value)) {
                $material = DaftarKodeMaterial::where('kode_material', $value)->first();
                $tool = Tools::where('kode_material', $material->kode_material)->first();

                if ($material) {
                    $this->items[$index]['deskripsi'] = $material->deskripsi;
                    $this->items[$index]['spesifikasi'] = $material->spesifikasi;
                    $this->items[$index]['satuan'] = $material->satuan;
                    if ($tool) {
                        $this->items[$index]['kategori'] = $tool->jenis_material;
                    }
                } else {
                    $this->items[$index]['error'] = "Kode material '{$value}' tidak ditemukan.";
                }
            }
            $this->recheckAllDuplicateErrors();
        }
    }

    protected function recheckAllDuplicateErrors()
    {
        foreach ($this->items as $idx => $item) {
            if ($item['error'] !== null && strpos($item['error'], 'sudah ada di baris lain') !== false) {
                if (strpos($item['error'], 'tidak ditemukan') === false) {
                    $this->items[$idx]['error'] = null;
                } else {
                    $this->items[$idx]['error'] = str_replace(' (Juga duplikat)', '', $this->items[$idx]['error']);
                }
            }
        }

        $kodeMaterials = [];
        foreach ($this->items as $idx => $item) {
            if (! empty($item['kode_material'])) {
                $kodeMaterials[$idx] = $item['kode_material'];
            }
        }

        $seen = [];
        $duplicates = [];
        foreach ($kodeMaterials as $idx => $kode_material) {
            if (in_array($kode_material, $seen)) {
                $duplicates[] = $kode_material;
            } else {
                $seen[] = $kode_material;
            }
        }

        foreach ($this->items as $idx => $item) {
            if (empty($item['kode_material'])) {
                continue;
            }

            if (in_array($item['kode_material'], $duplicates)) {
                if ($this->items[$idx]['error'] === null) {
                    $this->items[$idx]['error'] = "Kode material '{$item['kode_material']}' sudah ada di baris lain.";
                } elseif (strpos($this->items[$idx]['error'], 'sudah ada di baris lain') === false) {
                    $this->items[$idx]['error'] .= ' (Juga duplikat)';
                }
            }
        }
    }

    public function render()
    {
        return view('livewire.spm.resubmit-spm');
    }

    public function update(SpmItemService $service, CreateSpmFileService $createSpmFileService)
    {
        $this->validate();

        DB::beginTransaction();
        try {
            $this->spm->update([
                'project_id' => $this->project_id,
                'posisi_ct_id' => $this->posisi_ct_id,
                'kepada' => $this->kepada,
                'tanggal_spm' => now(),
                'destinasi' => $this->destinasi,
                'status' => 'WAITING',
                'departemen_id' => $this->destinasi === 'workshop' ? $this->departemen_id : null,
                'bagian_id' => $this->destinasi === 'workshop' ? $this->bagian_id : null,
                'unit_id' => $this->destinasi === 'workshop' ? $this->unit_id : null,
            ]);

            // Hapus item lama
            SpmItem::where('spm_id', $this->spm->id)->delete();

            // Simpan item baru
            $service->saveItems($this->items, $this->posisi_ct_id, $this->project_id, $this->spm->id);
            $filePath = $createSpmFileService->createSpmFile($this->spm);
            $this->spm->update([
                'file' => $filePath,
            ]);
            DB::commit();
            session()->flash('success', 'SPM berhasil diajukan ulang.');

            return redirect()->route('spm.index');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Gagal mengajukan ulang SPM: ' . $e->getMessage());
        }
    }
}
