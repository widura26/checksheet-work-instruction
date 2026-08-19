<?php

namespace App\Livewire\Distribusi\Penyerahan;

use App\Models\Bagian;
use App\Models\DaftarDistribusiTool;
use App\Models\Departemen;
use App\Models\DistribusiTool;
use App\Models\Karyawan;
use App\Models\Posisi_CT;
use App\Models\Project;
use App\Models\SatuanKonversi;
use App\Models\ToolProjectStock;
use App\Models\Tools;
use App\Models\Unit;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class CreateForm extends Component
{
    public $nama_peminjam;
    public $nip_peminjam;
    public $departemen_id;
    public $posisi_ct_departemen_id;
    public $bagian_id;
    public $unit_id;
    public $project_id;
    public $toolItems = [];
    public $apdItems = [];
    public $consumableItems = [];
    public $departemens = [];
    public $bagians = [];
    public $units = [];
    public $projects = [];
    public $keterangan;
    public $backDateAdmin;
    protected function rules()
    {
        return [
            'nama_peminjam' => 'required|string|max:255',
            'nip_peminjam' => 'required|string|max:100',
            'departemen_id' => 'required|exists:departemens,id',
            'bagian_id' => 'required|exists:bagians,id',
            'unit_id' => 'nullable|exists:units,id',
            'project_id' => 'required|exists:projects,project_id',
            'keterangan' => 'nullable',

            // Validasi untuk toolItems
            'toolItems.*.kode_material' => 'nullable|exists:tools,kode_material',
            'toolItems.*.qty' => 'required_with:toolItems.*.kode_material|numeric|integer|min:1',
            'toolItems.*.posisi_ct_id' => 'required_with:toolItems.*.kode_material',
            'toolItems.*.tool_id' => 'required_with:toolItems.*.kode_material|exists:tools,id',

            // Validasi untuk apdItems
            'apdItems.*.kode_material' => 'nullable|exists:tools,kode_material',
            'apdItems.*.qty' => 'required_with:apdItems.*.kode_material|numeric|integer|min:1',
            'apdItems.*.posisi_ct_id' => 'required_with:apdItems.*.kode_material',
            'apdItems.*.tool_id' => 'required_with:apdItems.*.kode_material|exists:tools,id',

            // Validasi untuk consumableItems
            'consumableItems.*.kode_material' => 'nullable|exists:tools,kode_material',
            'consumableItems.*.qty' => 'required_with:consumableItems.*.kode_material|numeric|integer|min:1',
            'consumableItems.*.posisi_ct_id' => 'required_with:consumableItems.*.kode_material',
            'consumableItems.*.tool_id' => 'required_with:consumableItems.*.kode_material|exists:tools,id',
        ];
    }

    protected $messages = [
        'nama_peminjam.required' => 'Nama peminjam wajib diisi.',
        'nip_peminjam.required' => 'NIP peminjam wajib diisi.',
        'departemen_id.required' => 'Departemen wajib dipilih.',
        'project_id.required' => 'Project wajib dipilih.',
        'bagian_id.required' => 'Bagian wajib dipilih.',

        'toolItems.*.qty.required_with' => 'Jumlah material wajib diisi.',
        'toolItems.*.qty.min' => 'Jumlah material minimal 1.',
        'toolItems.*.qty.integer' => 'Jumlah material tidak boleh decimal.',
        'apdItems.*.qty.required_with' => 'Jumlah material wajib diisi.',
        'apdItems.*.qty.min' => 'Jumlah material minimal 1.',
        'apdItems.*.qty.integer' => 'Jumlah material tidak boleh decimal.',
        'consumableItems.*.qty.required_with' => 'Jumlah material wajib diisi.',
        'consumableItems.*.qty.min' => 'Jumlah material minimal 1.',
        'consumableItems.*.qty.integer' => 'Jumlah material tidak boleh decimal.',
    ];

    public function mount()
    {
        if (! Auth::user()->hasRole(['admin', 'staff'])) {
            abort(403);
        }
        $this->departemens = Departemen::orderBy('nama_departemen', 'asc')->where('is_active', true)->get();
        $this->bagians = collect();
        $this->units = collect();
        $this->projects = Project::whereNull('closed_at')->get();

        // Inisialisasi baris kosong untuk masing-masing kategori
        $this->toolItems = [$this->emptyRow()];
        $this->apdItems = [$this->emptyRow()];
        $this->consumableItems = [$this->emptyRow()];
    }

    private function emptyRow()
    {
        return [
            'tool_id' => '',
            'kode_material' => '',
            'posisi_ct_id' => '',
            'available_posisi' => [],
            'deskripsi' => '',
            'stok_tersedia' => '',
            'spesifikasi' => '',
            'qty' => '',
            'satuan' => '',
            'has_konversi' => false,
            'konversi_detail' => null,
        ];
    }

    public function updatedDepartemenId($value)
    {
        $this->resetItems();
        $departemen = collect($this->departemens)->where('id', (int) $value)->first();
        // if ($departemen) {
        //     $keyword = explode(' ', $departemen->nama_departemen)[0];
        //     $ct = Posisi_CT::where('nama_posisi_ct', 'LIKE', "%{$keyword}%")->first()->id ?? null;
        //     $this->posisi_ct_departemen_id = $ct ?? 'ppc';
        // }
        if ($departemen) {
            $ct = Posisi_CT::where('departemen_id', $departemen->id)->first()->id ?? null;
            if ($ct) {
                $this->posisi_ct_departemen_id = $ct;
            } else {
                $this->posisi_ct_departemen_id = Posisi_CT::whereNull('departemen_id')->first()->id ?? null;
                // kunci agar CT dan project sesuai dengan departemen, karena project UU bisa di akses
                // oleh semua departemen maka di buat kunci agar jika tidak ada posisi ct
                // yang sesuai dengan departemen maka akan menggunakan posisi ct default
                // yang tidak memiliki departemen, dan project yang otomatis muncul adalah project UU
                $this->project_id = $this->posisi_ct_departemen_id ? 'UU' : null;
            }
        }
        $this->bagians = Bagian::where('departemen_id', $value)->get();
        $this->reset('bagian_id', 'unit_id');
    }

    public function updatedBagianId($value)
    {
        $this->units = Unit::where('bagian_id', $value)->get();
        $this->reset('unit_id');
    }

    public function updatedProjectId()
    {
        $this->resetItems();
    }

    private function resetItems()
    {
        $this->toolItems = [$this->emptyRow()];
        $this->apdItems = [$this->emptyRow()];
        $this->consumableItems = [$this->emptyRow()];
    }

    public function updatedToolItems($value, $key)
    {
        $this->handleUpdatedItems('tool', $key, $value);
    }

    public function updatedApdItems($value, $key)
    {
        $this->handleUpdatedItems('apd', $key, $value);
    }

    public function updatedConsumableItems($value, $key)
    {
        $this->handleUpdatedItems('consumable', $key, $value);
    }

    private function handleUpdatedItems($category, $key, $value)
    {
        $parts = explode('.', $key);
        $index = $parts[0];
        $field = $parts[1];
        $prop = $category . 'Items';

        if (! $this->departemen_id) {
            $this->dispatch('show-alert', ['message' => 'Pilih departemen terlebih dahulu.']);
            $this->{$prop}[$index]['kode_material'] = '';

            return;
        }
        if (empty($this->project_id)) {
            $this->dispatch('show-alert', ['message' => 'Pilih project terlebih dahulu.']);
            $this->{$prop}[$index]['kode_material'] = '';

            return;
        }
        // kode ini awalnya di buat agar user bisa memilih material dari CT mana
        // sedangkan sekarang sudah di buat filter posisi ct berdasarkan departemen, jadi kode ini tetap di pertahankan
        // kode ini sudah bisa tidak ada masalah
        // if ($field === 'posisi_ct_id') {
        //     $posisi = collect($this->{$prop}[$index]['available_posisi'])->firstWhere('posisi_ct_id', $value);
        //     if ($posisi) {
        //         $this->selectPosisi($index, $posisi, $category);
        //     }
        // }
    }

    public function onKodeMaterialChanged($index, $category)
    {
        $prop = $category . 'Items';
        $value = $this->{$prop}[$index]['kode_material'];

        $this->{$prop}[$index]['posisi_ct_id'] = null;
        $this->{$prop}[$index]['available_posisi'] = [];

        if (! $value || ! $this->project_id) {
            $this->resetToolRow($index, $category);

            return;
        }

        $stocks = ToolProjectStock::with(['tool.posisiCT'])
            ->whereHas('tool', function ($q) use ($value, $category) {
                return $q->where('kode_material', trim($value))
                    ->where('jenis_material', $category);
            })
            ->where('project_id', $this->project_id)
            ->where('qty', '>', 0)
            ->get();

        if ($stocks->isEmpty()) {
            $this->dispatch('show-alert', ['message' => "Material tidak tersedia di kategori $category Atau Stok Sudah Habis"]);
            $this->resetToolRow($index, $category);

            return;
        }
        // kode ini awalnya di buat agar user bisa memilih material dari CT mana
        // sedangkan sekarang sudah di buat filter posisi ct berdasarkan departemen, jadi kode ini tetap di pertahankan
        // kode ini sudah bisa tidak ada masalah
        $this->{$prop}[$index]['available_posisi'] = $stocks
            ->filter(function ($s) {
                return ($this->posisi_ct_departemen_id !== null)
                    ? $s->tool->posisiCT->id === $this->posisi_ct_departemen_id
                    : false;
            })
            ->map(fn($s) => [
                'tool_id' => $s->tool_id,
                'posisi_ct_id' => $s->tool->posisi_ct_id,
                'nama_posisi_ct' => $s->tool->posisiCT->nama_posisi_ct,
                'qty' => $s->qty_formatted,
            ])
            ->values()
            ->toArray();

        if (empty($this->{$prop}[$index]['available_posisi'])) {
            $this->dispatch('show-alert', ['message' => 'Material tidak tersedia di posisi ct dan departemen terpilih']);
            $this->resetToolRow($index, $category);

            return;
        }

        $hasKonversi = SatuanKonversi::where('kode_material', trim($value))->first();
        $this->{$prop}[$index]['has_konversi'] = (bool) $hasKonversi;
        if ($hasKonversi) {
            $this->{$prop}[$index]['konversi_detail'] = [
                'satuan_sap' => $hasKonversi->satuan_sap,
                'satuan_distribusi' => $hasKonversi->satuan_distribusi,
                'nilai_konversi' => $hasKonversi->nilai_konversi,
            ];
        }

        if (count($this->{$prop}[$index]['available_posisi']) === 1) {
            $this->selectPosisi($index, $this->{$prop}[$index]['available_posisi'][0], $category);
        } else {
            $this->dispatch('show-alert', ['message' => 'Data Stok Yang Dimasukkan Tidak Sesuai Dengan Ketentuan Aplikasi']);
            $this->resetToolRow($index, $category);
            return;
        }

        if (! $this->validateDuplicateAcrossCategories()) {
            $this->dispatch('focus-kode-material', index: $index, category: $category);

            return;
        }

        $this->dispatch('focus-qty', index: $index, category: $category);
    }

    private function validateDuplicateAcrossCategories()
    {
        $allCodes = [];
        $categories = ['tool', 'apd', 'consumable'];

        foreach ($categories as $cat) {
            $prop = $cat . 'Items';
            foreach ($this->{$prop} as $index => $item) {
                $code = trim($item['kode_material'] ?? '');
                if ($code === '') {
                    continue;
                }

                if (isset($allCodes[$code])) {
                    $this->dispatch('show-alert', ['message' => 'Kode Material Sudah Di Pilih Di Baris Lain.']);
                    $this->resetToolRow($index, $cat);

                    return false;
                }
                $allCodes[$code] = true;
            }
        }

        return true;
    }

    private function resetToolRow($index, $category)
    {
        $prop = $category . 'Items';
        $this->{$prop}[$index] = $this->emptyRow();
    }

    public function addTool($category)
    {
        $prop = $category . 'Items';
        $this->{$prop}[] = $this->emptyRow();
        $index = count($this->{$prop}) - 1;
        $this->dispatch('focus-kode-material', index: $index, category: $category);
    }

    private function selectPosisi($index, $posisi, $category)
    {
        $prop = $category . 'Items';
        $tool = Tools::find($posisi['tool_id']);

        $this->{$prop}[$index]['tool_id'] = $tool->id;
        $this->{$prop}[$index]['deskripsi'] = $tool->deskripsi;
        $this->{$prop}[$index]['spesifikasi'] = $tool->spesifikasi;
        $this->{$prop}[$index]['satuan'] = $tool->satuan;
        $this->{$prop}[$index]['stok_tersedia'] = $posisi['qty'];
        $this->{$prop}[$index]['posisi_ct_id'] = $posisi['posisi_ct_id'];
    }

    public function removeTool($index, $category)
    {
        $prop = $category . 'Items';
        unset($this->{$prop}[$index]);
        $this->{$prop} = array_values($this->{$prop});
        if (empty($this->{$prop})) {
            $this->{$prop} = [$this->emptyRow()];
        }
    }

    public function save()
    {
        // Cleanup baris kosong
        $categories = ['tool', 'apd', 'consumable'];
        foreach ($categories as $cat) {
            $prop = $cat . 'Items';
            $this->{$prop} = array_filter($this->{$prop}, fn($item) => ! empty(trim($item['kode_material'] ?? '')));
        }

        // Pastikan minimal ada satu item yang diisi
        if (empty($this->toolItems) && empty($this->apdItems) && empty($this->consumableItems)) {
            $this->dispatch('show-alert', ['message' => 'Minimal harus ada satu material yang dipilih.']);
            $this->resetItems();

            return;
        }

        $this->validate();

        DB::beginTransaction();
        try {
            $departemen = Departemen::find($this->departemen_id);
            $bagian = Bagian::find($this->bagian_id);
            $unit = Unit::find($this->unit_id);
            if ($departemen->is_active == false) {
                $this->dispatch('show-alert', ['message' => 'Departemen yang dipilih sudah non aktif']);
                $this->resetItems();
                return;
            }
            if ($bagian->is_active == false) {
                $this->dispatch('show-alert', ['message' => 'Bagian yang dipilih sudah non aktif']);
                return;
            }
            if ($unit->is_active == false) {
                $this->dispatch('show-alert', ['message' => 'Unit yang dipilih sudah non aktif']);
                return;
            }
            /*
            Proses ini menggunakan 2 level looping:

             1. Loop pertama ($categories)
                - Berfungsi untuk membuat data parent (DistribusiTool)
                - Setiap kategori (misal: tool, consumable) akan menghasilkan
                  1 data parent selama memiliki item

             2. Loop kedua ($this->{$items})
                - Berfungsi untuk membuat data child (DaftarDistribusiTool)
                - Semua item dalam kategori yang sama akan dimasukkan
                  ke dalam parent yang sama

             Alur logika:
             - Jika dalam 1 kategori terdapat beberapa item:
                 → 1 parent + banyak child

             - Jika terdapat beberapa kategori dengan masing-masing item:
                 → Setiap kategori akan memiliki 1 parent sendiri
                   dan child sesuai jumlah item dalam kategori tersebut

             Contoh:
             - consumable: 2 item -> 1 parent + 2 child
             - tool: 1 item, consumable: 1 item -> 2 parent + masing-masing 1 child

             Catatan:
             - Pengecekan empty($this->{$items}) digunakan untuk skip kategori
               yang tidak memiliki item agar tidak membuat parent kosong
            */
            foreach ($categories as $cat) {
                $items = $cat . 'Items';
                if (empty($this->{$items})) {
                    continue;
                }

                $distribusi = DistribusiTool::create([
                    'nama_peminjam' => $this->nama_peminjam,
                    'user_id' => Auth::id(),
                    'nip' => $this->nip_peminjam,
                    'keterangan' => $this->keterangan,
                    'departemen_id' => $this->departemen_id,
                    'bagian_id' => $this->bagian_id,
                    'unit_id' => $this->unit_id,
                    'kategori' => $cat,
                    'no_bast' => $cat === 'tool' ? $this->generateBeritaAcaraNumber() : null,
                    'project_id' => $this->project_id,
                    'type' => 'penyerahan',
                    'tanggal_pinjam_diserahkan' => now(),
                    'status' => 'diserahkan',
                ]);
                // khusus admin
                if (Auth::user()->hasRole('admin')) {
                    if ($this->backDateAdmin == null) {
                        $this->backDateAdmin = now();
                    }
                    $distribusi->update([
                        'tanggal_pinjam_diserahkan' => $this->backDateAdmin,
                        'created_at' => $this->backDateAdmin,
                    ]);
                }
                foreach ($this->{$items} as $toolData) {
                    $stock = ToolProjectStock::where('tool_id', $toolData['tool_id'])
                        ->where('project_id', $this->project_id)
                        ->where('posisi_ct_id', $toolData['posisi_ct_id'])
                        ->lockForUpdate()
                        ->first();
                    if (floor($toolData['qty']) != $toolData['qty']) {
                        throw new \Exception("Qty untuk Material {$toolData['kode_material']} tidak boleh decimal.");
                    }
                    $qtyToDecrement = $toolData['qty'];
                    if ($toolData['has_konversi']) {
                        $konversi = SatuanKonversi::where('kode_material', $toolData['kode_material'])->first();
                        if ($konversi) {
                            $qtyToDecrement = $konversi->convertToBase($toolData['qty']);
                        }
                    }

                    if (! $stock || $stock->qty < $qtyToDecrement) {
                        throw new \Exception("Stok untuk Material {$toolData['kode_material']} tidak mencukupi.");
                    }

                    $stock->decrement('qty', $qtyToDecrement);

                    $dft = DaftarDistribusiTool::create([
                        'distribusi_tools_id' => $distribusi->id,
                        'tool_id' => $toolData['tool_id'],
                        'posisi_ct_id' => $toolData['posisi_ct_id'],
                        'jumlah_distribusi' => $qtyToDecrement,
                        'jumlah_lapangan' => $toolData['has_konversi'] ? $toolData['qty'] : null,
                        'satuan_lapangan' => $toolData['has_konversi'] ? $toolData['konversi_detail']['satuan_distribusi'] : null,
                        'nilai_konversi' => $toolData['has_konversi'] ? $toolData['konversi_detail']['nilai_konversi'] : null,
                    ]);
                    // khusus admin
                    if (Auth::user()->hasRole('admin')) {
                        if ($this->backDateAdmin == null) {
                            $this->backDateAdmin = now();
                        }
                        $dft->update([
                            'created_at' => $this->backDateAdmin,
                        ]);
                    }
                }
            }

            DB::commit();
            $this->dispatch('swal-success', message: 'Distribusi berhasil disimpan.');
            $this->js('setTimeout(() => location.reload(), 1000)');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('show-alert', ['message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
            Log::error('Error saat menyimpan penyerahan distribusi: ' . $e->getMessage());
        }
    }

    private function generateBeritaAcaraNumber()
    {
        $year = date('Y');
        $kodeBagian = DB::table('kode_bagian_spm')->first();
        $lastToolDistribusi = DistribusiTool::where('kategori', 'tool')->whereYear('created_at', $year)->count();
        $runningNumber = str_pad($lastToolDistribusi + 1, 4, '0', STR_PAD_LEFT);

        return 'BAST-' . $runningNumber . '/' . $kodeBagian->kode . '/INKA/' . $year;
    }

    // untuk mencari data penerima berdasarkan nip, dan menampilkan nama penerima secara otomatis jika di enter
    public function searchNipData()
    {
        $penerima = Karyawan::where('nip', $this->nip_peminjam)->first();
        if ($penerima) {
            $this->nama_peminjam = $penerima->nama;
            $this->departemen_id = (string) $penerima->departemen_id;
            $this->updatedDepartemenId($this->departemen_id);
            $this->bagian_id = (string) $penerima->bagian_id;
            $this->updatedBagianId($this->bagian_id);
            $this->unit_id = (string) $penerima->unit_id;
            $this->resetErrorBag('nip_peminjam');
        } else {
            $this->addError('nip_peminjam', 'Data penerima tidak ditemukan. Pastikan NIP sudah benar, jika belum terdaftar silahkan ketik nama penerima dan pilih departemen, bagian, unit secara manual.');
        }
    }

    public function render()
    {
        return view('livewire.distribusi.penyerahan.create-form');
    }
}
