<?php

namespace App\Livewire\Material;

use App\Models\DaftarKodeMaterial;
use App\Models\Posisi_CT;
use App\Models\Project;
use App\Models\ToolProjectStock;
use App\Models\Tools;
use App\Models\UpdateStockTool;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithFileUploads;

class MaterialsCreate extends Component
{
    use WithFileUploads;
    public $projectId = '';
    public $posisiCtId = '';
    public $dokumen_pengambilan = '';
    public $nomor_dokumen = '';
    public $dokumen_pengambilan_file;
    public $items = [];
    public $projects = [];
    public $posisiCts = [];

    protected function rules()
    {
        return [
            'projectId' => 'required|string|exists:projects,project_id',
            'posisiCtId' => 'required|integer|exists:posisi__c_t_s,id',
            'dokumen_pengambilan' => 'required|in:bpm,memo,surat_jalan',
            'nomor_dokumen' => 'required|string|max:255',
            'dokumen_pengambilan_file' => 'required|file|mimes:pdf,jpeg,png,jpg|max:2048',
            'items' => [
                'required',
                'array',
                'min:1',
                function ($attribute, $value, $fail) {
                    $kodeMaterials = array_column($value, 'kode_material');
                    if (count($kodeMaterials) !== count(array_unique($kodeMaterials))) {
                        $fail('Terdapat duplikasi kode material dalam daftar.');
                    }
                },
            ],
            'items.*.kode_material' => 'required|string|exists:daftar_kode_materials,kode_material',
            'items.*.jenis_material' => 'required|in:tool,consumable,apd',
            'items.*.qty' => 'required|numeric|min:0.0001',
            'items.*.vendor' => 'nullable|in:vendor,INKA',
            'items.*.keterangan_vendor' => 'nullable|string|max:1000',
            'items.*.deskripsi' => 'required|string',
            'items.*.spesifikasi' => 'nullable|string',
            'items.*.satuan' => 'required|string',
        ];
    }


    protected function messages()
    {
        return [
            'projectId.required' => 'Project harus dipilih.',
            'posisiCtId.required' => 'Posisi CT harus dipilih.',
            'dokumen_pengambilan.required' => 'Jenis dokumen harus dipilih.',
            'nomor_dokumen.required' => 'Nomor dokumen harus diisi.',
            'dokumen_pengambilan_file.required' => 'File dokumen harus di-upload.',
            'items.required' => 'Minimal harus ada 1 material yang ditambahkan.',
            'items.min' => 'Minimal harus ada 1 material yang ditambahkan.',
            'items.*.kode_material.required' => 'Kode material harus diisi.',
            'items.*.kode_material.exists' => 'Kode material tidak ditemukan di master data.',
            'items.*.jenis_material.required' => 'Jenis material harus dipilih.',
            'items.*.qty.required' => 'Jumlah harus diisi.',
            'items.*.qty.min' => 'Jumlah minimal 1.',
        ];
    }

    public function mount()
    {
        $this->projects = Project::withoutGlobalScope('onlyWithStock')->orderBy('nama_project')->get();
        $this->posisiCts = Posisi_CT::orderBy('nama_posisi_ct')->get();
    }

    private function createEmptyItem(): array
    {
        return [
            'kode_material' => '',
            'jenis_material' => '',
            'deskripsi' => '',
            'spesifikasi' => '',
            'satuan' => '',
            'qty' => '',
            'vendor' => '',
            'keterangan_vendor' => '',
            'mode' => null, // 'create_new_material', 'create_stock_for_existing_material', 'update_stock'
            'toolId' => null,
            'currentStock' => 0,
            'error' => null,
        ];
    }

    public function addItem()
    {
        $this->items[] = $this->createEmptyItem();
        $index = count($this->items) - 1;
        $this->dispatch('focus-kode-material', index: $index);
    }
    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
        $this->recheckAllDuplicateErrors();
    }

    // public function updated($propertyName)
    // {
    //     if (in_array($propertyName, ['posisiCtId', 'projectId'])) {
    //         foreach (array_keys($this->items) as $index) {
    //             $this->onKodeMaterialChanged($index, false);
    //         }
    //     }
    // }
    public function updatedProjectId()
    {
        $this->items = [
            $this->createEmptyItem()
        ];
    }
    public function updatedPosisiCtId()
    {
        $this->items = [
            $this->createEmptyItem()
        ];
    }
    public function onKodeMaterialChanged($index, $shouldFocus = true)
    {
        $item = $this->items[$index];
        if (empty($item['kode_material'])) {
            $this->removeItem($index);
        }
        $item['deskripsi'] = '';
        $item['spesifikasi'] = '';
        $item['satuan'] = '';
        $item['currentStock'] = 0;
        $item['toolId'] = null;
        $item['mode'] = null;
        $item['error'] = null;

        if (empty($item['kode_material']) || empty($this->posisiCtId) || empty($this->projectId)) {
            if ($shouldFocus) {
                $this->recheckAllDuplicateErrors();
            }
            return;
        }
        // Fetch master material data first
        $materialMaster = DaftarKodeMaterial::where('kode_material', $item['kode_material'])->first();
        if (!$materialMaster) {
            $item['error'] = 'Kode material tidak ditemukan di master data.';
            if ($shouldFocus) {
                $this->recheckAllDuplicateErrors();
            }
            return;
        }
        $item['deskripsi'] = $materialMaster->deskripsi;
        $item['spesifikasi'] = $materialMaster->spesifikasi;
        $item['satuan'] = $materialMaster->satuan;
        // Check for existing materials
        $existingTool = Tools::where('kode_material', $item['kode_material'])
            ->where('posisi_ct_id', $this->posisiCtId)
            ->first();

        if ($existingTool) {
            $item['toolId'] = $existingTool->id;
            if (empty($item['vendor'])) {
                $item['vendor'] = $existingTool->vendor;
            }
            // if (empty($item['keterangan_vendor'])) {
            //     $item['keterangan_vendor'] = $existingTool->keterangan_vendor;
            // }
            // Check if this material already has stock in the selected project
            $projectStock = ToolProjectStock::where('tool_id', $existingTool->id)
                ->where('posisi_ct_id', $this->posisiCtId)
                ->where('project_id', $this->projectId)
                ->first();

            if ($projectStock) {
                // Mode: Update existing stock
                $item['mode'] = 'update_stock';
                $item['currentStock'] = $projectStock->qty_formatted;
                $item['jenis_material'] = $existingTool->jenis_material;
            } else {
                // Mode: Add stock for an existing tool in a new project
                $item['mode'] = 'create_stock_for_existing_material';
                $item['currentStock'] = 0;
                $item['jenis_material'] = $existingTool->jenis_material;
            }
        } else {
            // Mode: Create a brand new tool and its stock
            $item['mode'] = 'create_new_material';
            $item['currentStock'] = 0;
        }

        $this->items[$index] = $item;
        if ($shouldFocus) {
            $this->recheckAllDuplicateErrors();
            $this->dispatch('focus-qty', index: $index);
        }
    }
    protected function recheckAllDuplicateErrors()
    {
        // Clear previous duplicate errors
        foreach ($this->items as $i => $item) {
            if (!empty($item['error']) && str_contains($item['error'], 'duplikat')) {
                $this->items[$i]['error'] = null;
            }
        }

        $kodeMaterials = [];

        foreach ($this->items as $index => $item) {
            if (empty($item['kode_material'])) {
                continue;
            }
            if (isset($kodeMaterials[$item['kode_material']])) {
                $this->items[$index]['error'] = 'Kode material duplikat dalam daftar.';
                $this->items[$kodeMaterials[$item['kode_material']]]['error']
                    = 'Kode material duplikat dalam daftar.';
            } else {
                $kodeMaterials[$item['kode_material']] = $index;
            }
        }
    }
    public function save()
    {
        // check posisi CT if project UU
        if ($this->projectId === 'UU') {
            $ct = Posisi_CT::find($this->posisiCtId);
            if ($ct->departemen_id !== null) {
                session()->flash('error', 'Untuk project UU harus menggunakan CT_UU');
                return;
            }
        }
        foreach (array_keys($this->items) as $index) {
            $this->onKodeMaterialChanged($index, false);
            // If any item has an error after re-validation, stop the save process.
            if ($this->items[$index]['error']) {
                session()->flash('error', 'Terdapat error pada salah satu baris material. Silakan periksa kembali.');
                return;
            }
        }
        $this->validate();

        DB::beginTransaction();
        try {
            // 1. Upload file
            $filePath = null;
            if ($this->dokumen_pengambilan_file) {
                $nomorDokumen = preg_replace('/[^A-Za-z0-9\-]/', '-', $this->nomor_dokumen);
                $filename = $nomorDokumen . '-' . time() . '.' . $this->dokumen_pengambilan_file->getClientOriginalExtension();
                $filePath = $this->dokumen_pengambilan_file->storeAs('dokumen_pengambilan', $filename, 'public');
            } else {
                throw new \Exception('Dokumen pengambilan harus diupload');
            }

            // 2. Process each item
            foreach ($this->items as $item) {
                $toolToUse = null;
                $qtySebelum = 0;
                $type = 'input_material'; // Default type

                if ($item['mode'] === 'update_stock') {
                    $toolToUse = Tools::find($item['toolId']);
                    $currentStockEntry = DB::table('tool_project_stocks')
                        ->where('tool_id', $item['toolId'])
                        ->where('posisi_ct_id', $this->posisiCtId)
                        ->where('project_id', $this->projectId)
                        ->first();
                    $qtySebelum = $currentStockEntry->qty;
                    $type = 'tambah_stok';

                    DB::table('tool_project_stocks')
                        ->where('id', $currentStockEntry->id)
                        ->increment('qty', $item['qty']);
                } elseif ($item['mode'] === 'create_new_material') {
                    $toolToUse = Tools::create([
                        'kode_material' => $item['kode_material'],
                        'deskripsi' => $item['deskripsi'],
                        'spesifikasi' => $item['spesifikasi'],
                        'satuan' => $item['satuan'],
                        'jenis_material' => $item['jenis_material'],
                        'vendor' => $item['vendor'],
                        // 'keterangan_vendor' => $item['keterangan_vendor'],
                        'posisi_ct_id' => $this->posisiCtId,
                        'tanggal_ditambahkan' => now(),
                    ]);

                    $qtySebelum = 0;
                    $type = 'input_material';

                    DB::table('tool_project_stocks')->insert([
                        'tool_id' => $toolToUse->id,
                        'posisi_ct_id' => $this->posisiCtId,
                        'project_id' => $this->projectId,
                        'qty' => (float) $item['qty'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                } elseif ($item['mode'] === 'create_stock_for_existing_material') {
                    $toolToUse = Tools::find($item['toolId']);
                    DB::table('tool_project_stocks')->insert([
                        'tool_id' => $toolToUse->id,
                        'posisi_ct_id' => $this->posisiCtId,
                        'project_id' => $this->projectId,
                        'qty' => (float) $item['qty'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $type = 'tambah_stok';
                }
                // 3. Create log for each item
                UpdateStockTool::create([
                    'user_id' => Auth::id(),
                    'tool_id' => $toolToUse->id,
                    'project_id' => $this->projectId,
                    'qty_sebelum' => $qtySebelum,
                    'qty_update' => (float) $item['qty'],
                    'qty_sesudah' => $qtySebelum + (float) $item['qty'],
                    'type' => $type,
                    'dokumen_pengambilan' => $this->dokumen_pengambilan,
                    'nomor_dokumen' => $this->nomor_dokumen,
                    'dokumen_pengambilan_path' => $filePath,
                    'vendor' => $item['vendor'],
                    'keterangan_vendor' => $item['keterangan_vendor'],
                    'tanggal_update' => now(),
                ]);
            }

            DB::commit();
            session()->flash('success', 'Stok berhasil diperbarui untuk ' . count($this->items) . ' item.');
            return redirect()->route('update-stok-tool.index');
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            foreach ($e->validator->errors()->getMessages() as $key => $messages) {
                if (str_starts_with($key, 'items.')) {
                    $parts = explode('.', $key);
                    $index = (int)$parts[1];
                    $this->items[$index]['error'] = $messages[0];
                }
            }
            session()->flash('error', 'Validasi gagal. Silakan periksa error pada setiap baris.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in Material Create save: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            session()->flash('error', 'Terjadi kesalahan sistem: ');
        }
    }

    public function render()
    {
        return view('livewire.material.materials-create');
    }
}
