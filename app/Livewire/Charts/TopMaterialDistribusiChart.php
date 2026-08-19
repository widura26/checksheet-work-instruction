<?php

namespace App\Livewire\Charts;

use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\Attributes\On;

class TopMaterialDistribusiChart extends Component
{
    public $selectedProject;
    public $data;
    public $selectedYear;

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
        $this->dispatch('projectUpdatedTopMaterialDistribusiChart', data: $this->data);
    }
    #[On('selectedYearChanged')]
    public function updateYearFromParent($year)
    {
        $this->selectedYear = $year;
        $this->getData();
        $this->dispatch('projectUpdatedTopMaterialDistribusiChart', data: $this->data);
    }
    private function getData()
    {
        $year = $this->selectedYear ?? date('Y');
        $query = DB::table('daftar_distribusi_tools')
            ->join('distribusi_tools', 'daftar_distribusi_tools.distribusi_tools_id', '=', 'distribusi_tools.id')
            ->join('tools', 'daftar_distribusi_tools.tool_id', '=', 'tools.id')
            ->select('tools.kode_material', DB::raw('SUM(daftar_distribusi_tools.jumlah_distribusi) as total_distribusi'))
            ->where('distribusi_tools.project_id', $this->selectedProject)
            ->where('distribusi_tools.status', '<>', 'batal')
            ->whereYear('distribusi_tools.tanggal_pinjam_diserahkan', $year)
            ->groupBy('tools.kode_material')
            ->orderBy('total_distribusi', 'desc')
            ->limit(5)
            ->get();
        $this->data = [
            'kode_material' => $query->pluck('kode_material'),
            'total_distribusi' => $query->pluck('total_distribusi'),
        ];
    }
    public function render()
    {
        return view('livewire.charts.top-material-distribusi-chart');
    }
}
