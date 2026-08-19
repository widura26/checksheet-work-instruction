<?php

namespace App\Livewire\TransferPosting;

use App\Models\DaftarKodeMaterial;
use App\Models\MaterialTransferPosting;
use App\Models\Posisi_CT;
use App\Models\Project;
use App\Models\ToolProjectStock;
use App\Models\Tools;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Component;

class Create extends Component
{
    public $from_project_id = '';
    public $from_posisi_ct_id = '';

    public $to_project_id = '';
    public $to_posisi_ct_id = '';

    public $transfer_date;

    public $items = [];

    public $from_project_search = '';
    public $to_project_search = '';

    public $projects = [];
    public $projectsDest = [];

    public $posisiCts = [];
    public $posisiCtsDest = [];

    public $availableTools = [];

    public $alertMessage = '';
    public $alertType = '';
    public function mount()
    {
        $this->projects = Project::whereNull('closed_at')->get();
        $this->projectsDest = Project::withoutGlobalScope('onlyWithStock')->get();
        $this->posisiCts = Posisi_CT::all();
        $this->posisiCtsDest = $this->posisiCts;
        $this->transfer_date = now()->format('Y-m-d');
        $this->items[] = ['kode_material' => '', 'posisi_ct_id' => '', 'qty' => 1, 'stock' => 0, 'posisi_name' => '', 'search' => ''];
    }

    public function updatedFromProjectId($projectId)
    {
        $this->reset('items', 'availableTools', 'from_posisi_ct_id');
        $this->alertMessage = '';
        $this->alertType = '';
        if ($projectId) {
            $existMaterialInProject = ToolProjectStock::where('project_id', $projectId)
                ->with('tool:id,kode_material,deskripsi', 'posisiCt:id,nama_posisi_ct')
                ->get();

            if ($existMaterialInProject->isEmpty()) {
                $this->alertMessage = 'Tidak ada stok material yang tersedia untuk project ini.';
                $this->alertType = 'warning';
            }
        }
        $this->items[] = ['kode_material' => '', 'posisi_ct_id' => '', 'qty' => 1, 'stock' => 0, 'posisi_name' => '', 'search' => ''];
    }
    public function updatedFromPosisiCtId($posisiCtId)
    {
        $this->reset('items', 'availableTools');
        if (!$posisiCtId) {
            $this->alertMessage = 'Posisi CT Tidak Valid.';
            $this->alertType = 'warning';
        }
        if (!$this->from_project_id) {
            $this->reset('items', 'availableTools', 'from_posisi_ct_id');
            $this->alertMessage = 'Pilih Project Asal terlebih Dahulu.';
            $this->alertType = 'warning';
            return;
        }
        if (!$this->to_project_id) {
            $this->alertMessage = 'Silahkan Pilih Project Tujuan';
            $this->alertType = 'warning';
        }
        if ($this->from_project_id && $this->to_project_id && $this->to_project_id) {
            $this->alertMessage = '';
            $this->alertType = '';
        }
        $this->availableTools = ToolProjectStock::where('project_id', $this->from_project_id)
            ->where('posisi_ct_id', $posisiCtId)
            ->with(['tool', 'posisiCt'])
            ->get();
        if ($this->availableTools->isEmpty()) {
            $this->alertMessage = 'Tidak ada stok material yang tersedia untuk posisi Ct ini.';
            $this->alertType = 'warning';
        }
        $this->items[] = ['kode_material' => '', 'posisi_ct_id' => '', 'qty' => 1, 'stock' => 0, 'posisi_name' => '', 'search' => ''];
    }
    public function updatedToPosisiCtId($posisiCtId)
    {
        $this->reset('items');
        if (!$this->from_posisi_ct_id) {
            $this->alertMessage = 'Silahkan Pilih Posisi CT Awal Terlebih Dahulu';
            $this->alertType = 'warning';
        }
        $this->items[] = ['kode_material' => '', 'posisi_ct_id' => '', 'qty' => 1, 'stock' => 0, 'posisi_name' => '', 'search' => ''];
    }
    public function updatedItems($value, $key)
    {
        $parts = explode('.', $key);
        $index = $parts[0];
        $field = $parts[1];

        $this->resetErrorBag("items.{$index}.{$field}");

        if ($field === 'kode_material') {
            if (empty($value)) {
                $this->items[$index]['stock'] = 0;
                $this->items[$index]['posisi_name'] = '';
                $this->items[$index]['posisi_ct_id'] = '';

                return;
            }

            [$kodeMaterial, $posisiId] = explode('|', $value);
            $stockItem = $this->availableTools->first(function ($item) use ($kodeMaterial, $posisiId) {
                $material = Tools::where('kode_material', $kodeMaterial)->where('posisi_ct_id', $posisiId)->first();
                return $item->tool_id == $material->id && $item->posisi_ct_id == $posisiId;
            });
            if ($stockItem) {
                $this->items[$index]['stock'] = $stockItem->qty_formatted;
                $this->items[$index]['posisi_name'] = $stockItem->posisiCt->nama_posisi_ct;
                $this->items[$index]['posisi_ct_id'] = $stockItem->posisi_ct_id;
            }

            if (! empty($this->to_project_id)) {
                $destMaterial = Tools::where('kode_material', $kodeMaterial)
                    ->where('posisi_ct_id', $this->to_posisi_ct_id)
                    ->first();
                if (! $destMaterial) {
                    $this->addError(
                        "items.{$index}.kode_material",
                        "Material {$kodeMaterial} tidak ditemukan di project dan posisi CT tujuan. Material Akan di Tambahkan Secara Otomatis Oleh Sistem."
                    );
                    return;
                }
                $destProjectStockExists = ToolProjectStock::where('project_id', $this->to_project_id)
                    ->where('tool_id', $destMaterial->id)
                    ->where('posisi_ct_id', $this->to_posisi_ct_id)
                    ->exists();

                if (! $destProjectStockExists) {
                    $this->addError(
                        "items.{$index}.kode_material",
                        "Material {$kodeMaterial} tidak ditemukan di project dan posisi CT tujuan. Material Akan di Tambahkan Secara Otomatis Oleh Sistem."
                    );
                }
            }
            //cek ada error duplikat di arrtribut kode_material
            $this->validateDuplicateKodeMaterial();
        }
    }

    public function updatedToProjectId($value)
    {
        $this->resetErrorBag('items.*.kode_material');
        $this->reset('items', 'availableTools');
        $this->alertMessage = '';
        $this->alertType = '';
        if (! empty($value)) {
            foreach ($this->items as $index => $item) {
                if (! empty($item['kode_material'])) {
                    [$kodeMaterial, $posisiId] = explode('|', $item['kode_material']);
                    $material = Tools::where('kode_material', $kodeMaterial)
                        ->where('posisi_ct_id', $posisiId)
                        ->first();
                    if (! $material) {
                        $tool = Tools::find($material->id);
                        $this->addError(
                            "items.{$index}.kode_material",
                            "Material {$tool->kode_material} tidak ditemukan di project tujuan."
                        );
                    }
                }
            }
        }
    }

    public function addItem()
    {
        $this->items[] = ['kode_material' => '', 'posisi_ct_id' => '', 'qty' => 1, 'stock' => 0, 'posisi_name' => '', 'search' => ''];
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }
    private function validateDuplicateKodeMaterial()
    {
        // foreach (array_keys($this->items) as $i) {
        //     $this->resetErrorBag("items.{$i}.kode_material");
        // }
        $map = [];
        foreach ($this->items as $index => $item) {
            $kodeMaterial = trim($item['kode_material'] ?? '');
            if ($kodeMaterial === '') {
                continue;
            }
            if (isset($map[$kodeMaterial])) {
                $this->addError(
                    "items.{$index}.kode_material",
                    "Kode material {$kodeMaterial} tidak boleh sama."
                );

                return;
            } else {
                $map[$kodeMaterial] = $index;
                // $this->resetErrorBag("items.{$index}.kode_material");
            }
        }
    }
    public function save()
    {
        $validated = $this->validate([
            'from_project_id' => 'required|exists:projects,project_id',
            'to_project_id' => 'required|exists:projects,project_id',
            'from_posisi_ct_id' => 'required|exists:posisi__c_t_s,id',
            'to_posisi_ct_id' => 'required|exists:posisi__c_t_s,id',
            'transfer_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.kode_material' => 'required',
            'items.*.posisi_ct_id' => 'required',
            'items.*.qty' => 'required|integer|min:1',
        ]);
        if ($this->from_project_id == $this->to_project_id && $this->from_posisi_ct_id == $this->to_posisi_ct_id) {
            $this->addError('to_posisi_ct_id', 'Project dan Posisi CT tujuan tidak boleh sama dengan Project dan Posisi CT asal.');
            return;
        }
        $hasError = false;
        foreach ($this->items as $index => $item) {
            [$kodeMaterial, $posisiId] = explode('|', $item['kode_material']);
            $stockFromTool = Tools::where('kode_material', $kodeMaterial)
                ->where('posisi_ct_id', $posisiId)
                ->first();
            if (! $stockFromTool) {
                $this->addError("items.{$index}.kode_material", "Material dengan kode {$kodeMaterial} tidak ditemukan di posisi CT asal.");
                $hasError = true;
                continue;
            }
            $toolId = $stockFromTool->id;
            $stock = ToolProjectStock::where('project_id', $this->from_project_id)
                ->where('tool_id', $toolId)
                ->where('posisi_ct_id', $this->from_posisi_ct_id)
                ->first();

            if (! $stock || $stock->qty < $item['qty']) {
                $this->addError("items.{$index}.qty", 'Stok tidak mencukupi. Tersedia: ' . ($stock->qty ?? 0));
                $hasError = true;
            }
            // mencari material di destinasi, jika tidak ditemukan maka lakukan create

            [$destMaterial, $destProjectStockExists] = $this->findOrCreateDestMaterial($kodeMaterial, $this->to_posisi_ct_id, $stockFromTool);
            if (! $destMaterial) {
                $this->addError("items.{$index}.kode_material", "Material dengan kode {$kodeMaterial} tidak ditemukan di posisi CT tujuan. Akan di Tambahkan Secara Otomatis Oleh Sistem.");
            }
            if (! $destProjectStockExists) {
                $this->addError("items.{$index}.kode_material", "Material {$kodeMaterial} tidak ditemukan di project tujuan. Akan di Tambahkan Secara Otomatis Oleh Sistem.");
                // $hasError = true;
            }
        }
        if ($hasError) {
            return;
        }
        try {
            if ($this->getErrorBag()->isNotEmpty()) {
                throw new \Exception("Transfer Posting Gagal Di Proses. Ada data yang tidak valid.");
                return;
            }
            DB::beginTransaction();

            $posting = MaterialTransferPosting::create([
                'transfer_code' => Str::uuid(),
                'from_project_id' => $this->from_project_id,
                'to_project_id' => $this->to_project_id,
                'from_posisi_ct_id' => $this->from_posisi_ct_id,
                'to_posisi_ct_id' => $this->to_posisi_ct_id,
                'transfer_date' => $this->transfer_date,
                'status' => 'ON_LOAN',
                'created_by' => Auth::id(),
            ]);

            foreach ($this->items as $item) {
                [$kodeMaterial, $posisiId] = explode('|', $item['kode_material']);
                $destMaterial = Tools::where('kode_material', $kodeMaterial)
                    ->where('posisi_ct_id', $this->to_posisi_ct_id)
                    ->first();
                if (!$destMaterial) {
                    throw new \Exception("Material dengan kode {$kodeMaterial}-{$this->to_posisi_ct_id} tidak ditemukan di posisi CT tujuan.");
                }
                $destProjectStock = ToolProjectStock::where('project_id', $this->to_project_id)
                    ->where('tool_id', $destMaterial->id)
                    ->where('posisi_ct_id', $this->to_posisi_ct_id)
                    ->lockForUpdate()
                    ->first();
                if (!$destMaterial) {
                    throw new \Exception("Material dengan kode {$kodeMaterial} tidak ditemukan di project tujuan.");
                }
                // Decrement stock from source
                $sourceMaterial = Tools::where('kode_material', $kodeMaterial)
                    ->where('posisi_ct_id', $this->from_posisi_ct_id)
                    ->first();
                if (!$destMaterial) {
                    throw new \Exception("Material dengan kode {$kodeMaterial} tidak ditemukan di project asal.");
                }
                $sourceStock = ToolProjectStock::where('project_id', $this->from_project_id)
                    ->where('tool_id', $sourceMaterial->id)
                    ->where('posisi_ct_id', $this->from_posisi_ct_id)
                    ->lockForUpdate()
                    ->first();
                $sourceStock->decrement('qty', $item['qty']);
                // Create posting item
                $posting->items()->create([
                    'from_tool_id' => $sourceMaterial->id,
                    'to_tool_id' => $destMaterial->id,
                    'qty' => $item['qty'],
                ]);
                // Increment stock in destination
                $destProjectStock->increment('qty', $item['qty']);
            }

            $posting->histories()->create([
                'action' => 'CREATED',
                'notes' => 'Transfer posting created by ' . Auth::user()->name,
                'user_id' => Auth::id(),
            ]);

            DB::commit();

            session()->flash('success', 'Transfer posting berhasil dibuat.');

            return $this->redirect('/transfer-posting', navigate: true);
        } catch (\Throwable $th) {
            DB::rollBack();
            $this->alertMessage = 'Terjadi kesalahan: ' . $th->getMessage();
        }
    }
    private function findOrCreateDestMaterial($kodeMaterial, $posisiCtId, $sourceMaterial)
    {
        try {
            DB::beginTransaction();
            $masterMaterial = DaftarKodeMaterial::where('kode_material', $kodeMaterial)->firstOrFail();
            $material = Tools::firstOrCreate(
                [
                    'kode_material' => $kodeMaterial,
                    'posisi_ct_id'  => $posisiCtId,

                ],
                [
                    'public_id' => Str::uuid(),
                    'kode_material' => $kodeMaterial,
                    'deskripsi' => $masterMaterial->deskripsi,
                    'spesifikasi' => $masterMaterial->spesifikasi,
                    'satuan' => $masterMaterial->satuan,
                    'vendor' => $sourceMaterial->vendor,
                    'keterangan_vendor' => $sourceMaterial->keterangan_vendor,
                    'jenis_material' => $sourceMaterial->jenis_material,
                    'tanggal_ditambahkan' => now(),
                    'posisi_ct_id' => $posisiCtId,
                ]
            );
            $projectExist = ToolProjectStock::firstOrCreate([
                'tool_id' => $material->id,
                'posisi_ct_id' => $posisiCtId,
                'project_id' => $this->to_project_id,
            ], [
                'qty' => 0,
            ]);
            DB::commit();
            return [$material, $projectExist];
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
    public function render()
    {
        return view('livewire.transfer-posting.create');
    }
}
