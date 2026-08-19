<?php

namespace App\Livewire\Charts;

use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\Attributes\On;

class TrendPemakaianBulananChart extends Component
{
    public $selectedProject;
    public $selectedYear;
    public $data;
    public function mount($selectedProject = null, $selectedYear = null)
    {
        $this->selectedProject = $selectedProject;
        $this->selectedYear = $selectedYear;
    }
    #[On('selectedProjectChanged')]
    public function updateProjectFromParent($projectId)
    {
        $this->selectedProject = $projectId;
        $this->getData();
        $this->dispatch('projectUpdatedTrendChart', data: $this->data);
    }
    #[On('selectedYearChanged')]
    public function updateYearFromParent($year)
    {
        $this->selectedYear = $year;
        $this->getData();
        $this->dispatch('projectUpdatedTrendChart', data: $this->data);
    }
    private function getData()
    {
        $tahun = $this->selectedYear ?? now()->year;
        $result = DB::table('daftar_distribusi_tools')
            ->join('distribusi_tools', 'daftar_distribusi_tools.distribusi_tools_id', '=', 'distribusi_tools.id')
            ->join('tools', 'daftar_distribusi_tools.tool_id', '=', 'tools.id')
            ->where('distribusi_tools.project_id', $this->selectedProject)
            ->where('distribusi_tools.status', '<>', 'batal')
            ->whereBetween('distribusi_tools.tanggal_pinjam_diserahkan', [
                "$tahun-01-01",
                "$tahun-12-31"
            ])
            ->selectRaw("
        MONTH(distribusi_tools.tanggal_pinjam_diserahkan) as month,

        FLOOR(SUM(CASE 
            WHEN tools.jenis_material = 'consumable'
            THEN daftar_distribusi_tools.jumlah_distribusi
            ELSE 0
        END)) as consumable,

        FLOOR(SUM(CASE 
            WHEN tools.jenis_material = 'tool'
            THEN daftar_distribusi_tools.jumlah_distribusi
            ELSE 0
        END)) as tool,

        FLOOR(SUM(CASE 
            WHEN tools.jenis_material = 'apd'
            THEN daftar_distribusi_tools.jumlah_distribusi
            ELSE 0
        END)) as apd
    ")
            ->groupBy('month')
            ->get()
            ->keyBy('month');
        $data = collect(range(1, 12))->map(function ($month) use ($result) {
            return [
                'month'       => $month,
                'consumable'  => (int) ($result[$month]->consumable ?? 0),
                'tool'        => (int) ($result[$month]->tool ?? 0),
                'apd'         => (int) ($result[$month]->apd ?? 0),
            ];
        });
        $this->data = [
            'labels' => $data->pluck('month')->map(function ($month) {
                return date('M', mktime(0, 0, 0, $month, 10));
            })->toArray(),
            'series' => [
                'consumable' => $data->pluck('consumable')->toArray(),
                'tool'       => $data->pluck('tool')->toArray(),
                'apd'        => $data->pluck('apd')->toArray(),
            ],
        ];
    }
    public function render()
    {
        return view('livewire.charts.trend-pemakaian-bulanan-chart');
    }
}
