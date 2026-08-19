<?php

namespace App\Livewire\Laporan;

use App\Exports\Laporan\DistribusiExport;
use App\Models\Bagian;
use App\Models\Departemen;
use App\Models\Project;
use App\Models\Tools;
use App\Models\Unit;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;

class Distribusi extends Component
{
    public $startDate;
    public $endDate;
    public $projectId;
    public $departemenId;
    public $bagianId;
    public $unitId;
    public $kodeMaterial;
    public $kategori;
    public $tipeDistribusi;
    public $projects;
    public $departemens;
    public $bagians = [];
    public $units = [];

    public function mount()
    {
        $this->projects = Project::whereNull('closed_at')->get();
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

    public function render()
    {
        $results = $this->getResultsQuery()->orderBy('tools.deskripsi')->get();

        return view('livewire.laporan.distribusi', [
            'results' => $results,
        ]);
    }

    private function getResultsQuery()
    {
        $distribusi = DB::table('daftar_distribusi_tools')
            ->join('distribusi_tools', 'daftar_distribusi_tools.distribusi_tools_id', '=', 'distribusi_tools.id')
            ->join('projects', 'distribusi_tools.project_id', '=', 'projects.project_id')
            ->join('units', 'distribusi_tools.unit_id', '=', 'units.id')
            ->join('bagians', 'units.bagian_id', '=', 'bagians.id')
            ->where('distribusi_tools.status', '<>', 'batal')
            ->when($this->startDate, function ($q) {
                $q->where(
                    'distribusi_tools.tanggal_pinjam_diserahkan',
                    '>=',
                    Carbon::parse($this->startDate)->startOfDay()
                );
            })
            ->when($this->endDate, function ($q) {
                $q->where(
                    'distribusi_tools.tanggal_pinjam_diserahkan',
                    '<=',
                    Carbon::parse($this->endDate)->endOfDay()
                );
            });
        // if ($this->projectId) {
        $distribusi->where('distribusi_tools.project_id', $this->projectId);
        // }
        if ($this->departemenId) {
            $distribusi->where('bagians.departemen_id', $this->departemenId);
        }
        if ($this->bagianId) {
            $distribusi->where('units.bagian_id', $this->bagianId);
        }
        if ($this->unitId) {
            $distribusi->where('distribusi_tools.unit_id', $this->unitId);
        }
        if ($this->tipeDistribusi) {
            $distribusi->where('distribusi_tools.type', $this->tipeDistribusi);
        }

        $distribusi = $distribusi->select(
            'daftar_distribusi_tools.tool_id',
            DB::raw('SUM(daftar_distribusi_tools.jumlah_distribusi) as total_distribusi'),
            'projects.project_id',
            'projects.nama_project',
        )
            ->groupBy('daftar_distribusi_tools.tool_id', 'projects.project_id', 'projects.nama_project');

        $results = Tools::query()
            ->with('posisiCT')
            ->select(
                'tools.id as tool_id',
                'tools.deskripsi',
                'tools.kode_material',
                'tools.spesifikasi',
                'tools.jenis_material',
                'tools.satuan',
                'tools.posisi_ct_id',
                'distribusi_counts.project_id',
                'distribusi_counts.nama_project'
            )
            ->leftJoinSub($distribusi, 'distribusi_counts', function ($join) {
                $join->on('tools.id', '=', 'distribusi_counts.tool_id');
            })
            ->addSelect(
                DB::raw('COALESCE(distribusi_counts.total_distribusi, 0) as total_distribusi'),
            )
            ->where(function ($query) use ($distribusi) {
                $query->whereIn('tools.id', $distribusi->pluck('tool_id'));
            });

        if ($this->kodeMaterial) {
            $results->where('tools.kode_material', 'like', '%' . $this->kodeMaterial . '%');
        }
        if ($this->kategori) {
            $results->where('tools.jenis_material', $this->kategori);
        }
        return $results;
    }

    public function resetFilters()
    {
        $this->reset(['projectId', 'departemenId', 'bagianId', 'unitId', 'kodeMaterial', 'startDate', 'endDate', 'tipeDistribusi', 'kategori']);
        $this->bagians = [];
        $this->units = [];
    }

    public function exportToExcel()
    {
        return Excel::download(
            new DistribusiExport(
                $this->startDate,
                $this->endDate,
                $this->projectId,
                $this->departemenId,
                $this->bagianId,
                $this->unitId,
                $this->kodeMaterial,
                $this->kategori,
                $this->tipeDistribusi
            ),
            now()->format('Y-m-d') . '_laporan-distribusi-material.xlsx'
        );
    }
}
