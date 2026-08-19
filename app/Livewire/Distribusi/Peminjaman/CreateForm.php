<?php

namespace App\Livewire\Distribusi\Peminjaman;

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

    public $project_id_dest;

    public $tools = [];

    public $departemens = [];

    public $bagians = [];

    public $units = [];

    public $projects = [];

    public $keterangan;

    public $posisiCt = '';

    protected function rules()
    {
        return [
            'nama_peminjam' => 'required|string|max:255',
            'keterangan' => 'nullable',
            'nip_peminjam' => 'required|string|max:100',
            'departemen_id' => 'required|exists:departemens,id',
            'project_id' => 'required|exists:projects,project_id',
            'bagian_id' => 'required|exists:bagians,id',
            'unit_id' => 'nullable|exists:units,id',
            'tools' => 'required|array|min:1',
            'tools.*.kode_material' => 'required',
            'tools.*.qty' => 'required|integer|min:1',
            'tools.*.posisi_ct_id' => 'required',
            'tools.*.tool_id' => 'required|exists:tools,id',
        ];
    }

    protected $messages = [
        'nama_peminjam.required' => 'Nama peminjam wajib diisi.',
        'nama_peminjam.string' => 'Nama peminjam harus berupa teks.',
        'nama_peminjam.max' => 'Nama peminjam maksimal 255 karakter.',

        'nip_peminjam.required' => 'NIP peminjam wajib diisi.',
        'nip_peminjam.string' => 'NIP peminjam harus berupa teks.',
        'nip_peminjam.max' => 'NIP peminjam maksimal 100 karakter.',

        'departemen_id.required' => 'Departemen wajib dipilih.',
        'departemen_id.exists' => 'Departemen yang dipilih tidak valid.',

        'project_id.required' => 'Project wajib dipilih.',
        'project_id.exists' => 'Project yang dipilih tidak valid.',

        'bagian_id.required' => 'Bagian wajib dipilih.',
        'bagian_id.exists' => 'Bagian yang dipilih tidak valid.',

        'unit_id.exists' => 'Unit yang dipilih tidak valid.',

        'tools.required' => 'Minimal harus ada satu tool yang dipilih.',
        'tools.array' => 'Format data tool tidak valid.',
        'tools.min' => 'Minimal harus ada satu tool yang dipilih.',

        'tools.*.kode_material.required' => 'Kode material wajib diisi.',
        'tools.*.qty.required' => 'Jumlah tool wajib diisi.',
        'tools.*.qty.integer' => 'Jumlah tool harus berupa angka.',
        'tools.*.qty.min' => 'Jumlah tool minimal 1.',

        'tools.*.posisi_ct_id.required' => 'Posisi tool wajib dipilih.',

        'tools.*.tool_id.required' => 'Tool wajib dipilih.',
        'tools.*.tool_id.exists' => 'Tool yang dipilih tidak valid.',
    ];


    public function mount()
    {
        if (!Auth::user()->hasRole(['admin', 'staff'])) {
            abort(403);
        }
        $this->departemens = Departemen::orderBy('nama_departemen', 'asc')->where('is_active', true)->get();
        $this->projects = Project::whereNull('closed_at')->get();
        $this->bagians = collect();
        $this->units = collect();
        $this->tools = [
            [
                'tool_id' => '',
                'kode_material' => '',
                'posisi_ct_id' => '',
                'available_posisi' => [],
                'deskripsi' => '',
                'spesifikasi' => '',
                'stok_tersedia' => '',
                'qty' => '',
                'satuan' => '',
            ],
        ];
    }

    public function updatedDepartemenId($value)
    {
        $this->tools = [
            ['tool_id' => '', 'kode_material' => '', 'deskripsi' => '', 'spesifikasi' => '', 'stok_tersedia' => '', 'posisiCt' => '', 'qty' => '', 'satuan' => ''],
        ];
        $departemen = collect($this->departemens)->where('id', (int) $value)->first();
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
        $this->tools = [
            ['tool_id' => '', 'kode_material' => '', 'deskripsi' => '', 'spesifikasi' => '', 'stok_tersedia' => '', 'posisiCt' => '', 'qty' => '', 'satuan' => ''],
        ];
    }

    public function updatedTools($value, $key)
    {
        [$index, $field] = explode('.', $key);
        if (!$this->departemen_id) {
            $this->dispatch('show-alert', ['message' => 'Pilih departemen terlebih dahulu.']);
            $this->tools[$index]['kode_material'] = '';
            return;
        }
        if (empty($this->project_id)) {
            $this->dispatch('show-alert', ['message' => 'Pilih project terlebih dahulu.']);
            $this->tools[$index]['kode_material'] = '';
            return;
        }
        if ($field === 'posisi_ct_id') {
            $posisi = collect($this->tools[$index]['available_posisi'])
                ->firstWhere('posisi_ct_id', $value);
            if ($posisi) {
                $this->selectPosisi($index, $posisi);
            }
        }
    }

    public function onKodeMaterialChanged($index)
    {
        $value = $this->tools[$index]['kode_material'];
        $this->tools[$index]['posisi_ct_id'] = null;
        $this->tools[$index]['available_posisi'] = [];
        if (! $value || ! $this->project_id) {
            $this->resetToolRow($index);
            return;
        }
        $stocks = ToolProjectStock::with(['tool.posisiCT'])
            ->whereHas(
                'tool',
                fn($q) => $q->where('kode_material', trim($value))
            )
            ->where('project_id', $this->project_id)
            ->where('qty', '>', 0)
            ->get();
        if ($stocks->isEmpty()) {
            $this->dispatch('show-alert', ['message' => 'Material tidak tersedia']);
            $this->resetToolRow($index);
            return;
        }
        // kode ini awalnya di buat agar user bisa memilih material dari CT mana
        // sedangkan sekarang sudah di buat filter posisi ct berdasarkan departemen, jadi kode ini tetap di pertahankan
        // kode ini sudah bisa tidak ada masalah
        $this->tools[$index]['available_posisi'] = $stocks
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
        if (empty($this->tools[$index]['available_posisi'])) {
            $this->dispatch('show-alert', ['message' => 'Material tidak tersedia di posisi ct dan departemen terpilih']);
            $this->resetToolRow($index);
        }
        $hasKonversi = SatuanKonversi::where('kode_material', trim($value))->first();
        if ($hasKonversi) {
            $this->tools[$index]['has_konversi'] = true;
            $this->tools[$index]['konversi_detail'] = [
                'satuan_sap' => $hasKonversi->satuan_sap,
                'satuan_distribusi' => $hasKonversi->satuan_distribusi,
                'nilai_konversi' => $hasKonversi->nilai_konversi,
            ];
        } else {
            $this->tools[$index]['has_konversi'] = false;
        }
        if (count($this->tools[$index]['available_posisi']) === 1) {
            $this->selectPosisi($index, $this->tools[$index]['available_posisi'][0]);
        }
        //cek ada error duplikat di arrtribut kode_material
        if (! $this->validateDuplicateKodeMaterial()) {
            $this->dispatch('focus-kode-material', index: $index);
            return;
        }
        //trigger untuk auto focus pada input qty
        $this->dispatch('focus-qty', index: $index);
    }
    private function validateDuplicateKodeMaterial()
    {
        $map = [];
        foreach ($this->tools as $index => $item) {
            $kodeMaterial = trim($item['kode_material'] ?? '');
            if ($kodeMaterial === '') {
                continue;
            }
            if (isset($map[$kodeMaterial])) {
                $this->dispatch('show-alert', ['message' => 'Kode Material Sudah Di Pilih Di Baris Lain.']);
                $this->resetToolRow($index);
                return false;
            } else {
                $map[$kodeMaterial] = $index;
            }
        }
        return true;
    }
    private function resetToolRow($index)
    {
        $this->tools[$index]['tool_id'] = '';
        $this->tools[$index]['kode_material'] = '';
        $this->tools[$index]['deskripsi'] = '';
        $this->tools[$index]['spesifikasi'] = '';
        $this->tools[$index]['satuan'] = '';
        $this->tools[$index]['posisi_ct_id'] = '';
        $this->tools[$index]['available_posisi'] = [];
        $this->tools[$index]['has_konversi'] = false;
        $this->tools[$index]['konversi_detail'] = null;
    }

    public function addTool()
    {
        $this->tools[] = ['tool_id' => '', 'kode_material' => '', 'deskripsi' => '', 'spesifikasi' => '', 'posisiCt' => '', 'qty' => '', 'satuan' => ''];
        $index = count($this->tools) - 1;

        $this->dispatch('focus-kode-material', index: $index);
    }

    public function removeTool($index)
    {
        unset($this->tools[$index]);
        $this->tools = array_values($this->tools);
    }

    private function selectPosisi($index, $posisi)
    {
        $tool = Tools::find($posisi['tool_id']);

        $this->tools[$index]['tool_id'] = $tool->id;
        $this->tools[$index]['deskripsi'] = $tool->deskripsi;
        $this->tools[$index]['spesifikasi'] = $tool->spesifikasi;
        $this->tools[$index]['satuan'] = $tool->satuan;
        $this->tools[$index]['stok_tersedia'] = $posisi['qty'];
        $this->tools[$index]['posisi_ct_id'] = $posisi['posisi_ct_id'];
    }

    public function save()
    {
        //hapus baris jika kode material kosong, karena menggunakan barcode scanner
        //aku membuat nya jika selesai scan langsung menambah baris baru, dan jika tidak di isi
        //yang jelas baris yang di generate otomatis tidak lolos validasi
        foreach ($this->tools as $index => $tool) {
            if (empty(trim($tool['kode_material'] ?? ''))) {
                $this->removeTool($index);
            }
        }
        $this->tools = array_values($this->tools);
        $this->validate();

        DB::beginTransaction();
        try {
            $distribusi = DistribusiTool::create([
                'nama_peminjam' => $this->nama_peminjam,
                'user_id' => Auth::id(),
                'nip' => $this->nip_peminjam,
                'departemen_id' => $this->departemen_id,
                'project_id' => $this->project_id,
                'project_id_dest' => $this->project_id_dest,
                'keterangan' => $this->keterangan,
                'bagian_id' => $this->bagian_id,
                'unit_id' => $this->unit_id,
                'type' => 'peminjaman',
                'tanggal_pinjam_diserahkan' => now(),
                'status' => 'belum_kembali',
            ]);

            foreach ($this->tools as $toolData) {
                if (empty($toolData['tool_id'])) {
                    $this->dispatch('show-alert', ['message' => 'Material belum dipilih secara lengkap.']);
                    return;
                }
                $projectStock = ToolProjectStock::where('tool_id', $toolData['tool_id'])
                    ->where('project_id', $this->project_id)
                    ->where('posisi_ct_id', $toolData['posisi_ct_id'])
                    ->lockForUpdate()
                    ->first();

                if (! $projectStock || $projectStock->qty < $toolData['qty']) {
                    $this->dispatch('show-alert', ['message' => 'Stok untuk tool ' . $toolData['kode_material'] . ' tidak mencukupi.']);
                    return;
                }

                $projectStock->decrement('qty', $toolData['qty']);

                DaftarDistribusiTool::create([
                    'distribusi_tools_id' => $distribusi->id,
                    'tool_id' => $toolData['tool_id'],
                    'posisi_ct_id' => $toolData['posisi_ct_id'],
                    'jumlah_distribusi' => $toolData['qty'],
                    'status' => 'dipinjam',
                ]);
            }
            DB::commit();
            $this->dispatch('swal-success', message: 'Distribusi berhasil disimpan.');
            $this->js('setTimeout(() => location.reload(), 1000)');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saat menyimpan peminjaman distribusi tool: ' . $e->getMessage());
            $this->dispatch('show-alert', ['message' => 'Terjadi kesalahan']);
        }
    }
    // untuk mencari data penerima berdasarkan nip, dan menampilkan nama penerima secara otomatis jika di enter
    public function searchNipData()
    {
        $penerima = Karyawan::where('nip', $this->nip_peminjam)->first();
        if ($penerima) {
            $this->nama_peminjam = $penerima->nama;
            $this->departemen_id = (string)$penerima->departemen_id;
            $this->updatedDepartemenId($this->departemen_id);
            $this->bagian_id = (string)$penerima->bagian_id;
            $this->updatedBagianId($this->bagian_id);
            $this->unit_id = (string)$penerima->unit_id;
            $this->resetErrorBag('nip_peminjam');
        } else {
            $this->addError('nip_peminjam', 'Data penerima tidak ditemukan. Pastikan NIP sudah benar, jika belum terdaftar silahkan ketik nama penerima dan pilih departemen, bagian, unit secara manual.');
        }
    }
    public function render()
    {
        return view('livewire.distribusi.peminjaman.create-form');
    }
}
