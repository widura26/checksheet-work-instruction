<?php

namespace App\Exports\Laporan;

use App\Models\Bagian;
use App\Models\Departemen;
use App\Models\Project;
use App\Models\Tools;
use App\Models\Unit;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;

class DistribusiExport implements FromCollection, WithEvents, WithHeadings, WithMapping
{
    protected $startDate;
    protected $endDate;
    protected $projectId;
    protected $departemenId;
    protected $bagianId;
    protected $unitId;
    protected $kodeMaterial;
    protected $filterData;
    protected $kategori;
    protected $tipeDistribusi;
    public function __construct($startDate, $endDate, $projectId, $departemenId, $bagianId, $unitId, $kodeMaterial, $kategori, $tipeDistribusi)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->projectId = $projectId;
        $this->departemenId = $departemenId;
        $this->bagianId = $bagianId;
        $this->unitId = $unitId;
        $this->kodeMaterial = $kodeMaterial;
        $this->kategori = $kategori;
        $this->tipeDistribusi = $tipeDistribusi;
        $this->filterData = $this->resolveFilterData();
    }

    protected function resolveFilterData(): array
    {
        $filters = [];

        if ($this->startDate) {
            $filters[] = 'Tanggal Mulai: ' . Carbon::parse($this->startDate)->format('d/m/Y');
        }
        if ($this->endDate) {
            $filters[] = 'Tanggal Akhir: ' . Carbon::parse($this->endDate)->format('d/m/Y');
        }
        if ($this->projectId) {
            $project = Project::find($this->projectId);
            if ($project) {
                $filters[] = 'Project: ' . $project->nama_project;
            }
        }
        if ($this->departemenId) {
            $departemen = Departemen::find($this->departemenId);
            if ($departemen) {
                $filters[] = 'Departemen: ' . $departemen->nama_departemen;
            }
        }
        if ($this->bagianId) {
            $bagian = Bagian::find($this->bagianId);
            if ($bagian) {
                $filters[] = 'Bagian: ' . $bagian->nama_bagian;
            }
        }
        if ($this->unitId) {
            $unit = Unit::find($this->unitId);
            if ($unit) {
                $filters[] = 'Unit: ' . $unit->nama_unit;
            }
        }
        if ($this->kodeMaterial) {
            $filters[] = 'Kode Material: ' . $this->kodeMaterial;
        }

        return $filters;
    }

    public function collection(): Collection
    {
        $distribusi = DB::table('daftar_distribusi_tools')
            ->join('distribusi_tools', 'daftar_distribusi_tools.distribusi_tools_id', '=', 'distribusi_tools.id')
            ->join('projects', 'distribusi_tools.project_id', '=', 'projects.project_id')
            ->join('units', 'distribusi_tools.unit_id', '=', 'units.id')
            ->join('bagians', 'units.bagian_id', '=', 'bagians.id')
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
        if ($this->projectId) {
            $distribusi->where('distribusi_tools.project_id', $this->projectId);
        }
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
            ->addSelect(DB::raw('COALESCE(distribusi_counts.total_distribusi, 0) as total_distribusi'))
            ->where(function ($query) use ($distribusi) {
                $query->whereIn('tools.id', $distribusi->pluck('tool_id'));
            });

        if ($this->kodeMaterial) {
            $results->where('tools.kode_material', 'like', '%' . $this->kodeMaterial . '%');
        }
        if ($this->kategori) {
            $results->where('tools.jenis_material', $this->kategori);
        }
        return $results->orderBy('tools.deskripsi')->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Kode Material',
            'Deskripsi',
            'Spesifikasi',
            'Jenis Material',
            'Satuan',
            'Posisi',
            'Project',
            'Total Distribusi',
        ];
    }

    public function map($row): array
    {
        $no = $row->getKey() + 1;
        return [
            $no,
            $row->kode_material,
            $row->deskripsi,
            $row->spesifikasi,
            strtoupper($row->jenis_material),
            $row->satuan,
            $row->posisiCT->nama_posisi_ct,
            $row->nama_project,
            rtrim(rtrim(number_format($row->total_distribusi, 4, '.', ''), '0'), '.'),
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $filterRowCount = count($this->filterData);

                if ($filterRowCount > 0) {
                    $sheet->insertNewRowBefore(1, $filterRowCount + 2); // +2 for title and empty row

                    // Title
                    $sheet->setCellValue('A1', 'Laporan Distribusi');
                    $sheet->mergeCells('A1:' . $sheet->getHighestColumn() . '1');
                    $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
                    $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

                    // Add filter details
                    foreach ($this->filterData as $index => $filter) {
                        $sheet->setCellValue('A' . ($index + 3), $filter);
                        $sheet->mergeCells('A' . ($index + 3) . ':' . $sheet->getHighestColumn() . ($index + 3));
                        $sheet->getStyle('A' . ($index + 3))->getFont()->setBold(true);
                    }
                }
            },
        ];
    }
}
