<?php

namespace App\Livewire\Charts;

use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\Attributes\On;

class PersentaseDistribusiChart extends Component
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
        $this->dispatch('projectUpdatedPresentasiDistribusiChart', data: $this->data);
    }
    #[On('selectedYearChanged')]
    public function updateYearFromParents($year)
    {
        $this->selectedYear = $year;
        $this->getData();
        $this->dispatch('projectUpdatedPresentasiDistribusiChart', data: $this->data);
    }
    private function getData()
    {
        $tahun = $this->selectedYear ?? now()->year;
        $result = DB::table('daftar_distribusi_tools')
            ->join('distribusi_tools', 'daftar_distribusi_tools.distribusi_tools_id', '=', 'distribusi_tools.id')
            ->join('tools', 'daftar_distribusi_tools.tool_id', '=', 'tools.id')
            ->where('distribusi_tools.project_id', $this->selectedProject)
            ->where('distribusi_tools.status', '<>', 'batal')
            ->whereYear('distribusi_tools.tanggal_pinjam_diserahkan', $tahun)
            ->selectRaw('
                 COUNT(CASE 
                    WHEN tools.jenis_material = "tool"
                    THEN daftar_distribusi_tools.id
                END) AS tool,
                
                COUNT(CASE 
                    WHEN tools.jenis_material = "consumable"
                    THEN daftar_distribusi_tools.id
                END) AS consumable,

                COUNT(CASE 
                    WHEN tools.jenis_material = "apd"
                    THEN daftar_distribusi_tools.id
                END) AS apd
            ')
            ->first();
        $result = (array) $result;
        $this->data = [
            'total_distribusi' => array_values($result),
            'jenis_material' => array_map(function ($item) {
                if (strtolower($item) === 'apd') {
                    return strtoupper($item);
                }
                return ucfirst($item);
            }, array_keys($result))
        ];
    }
    public function render()
    {
        return view('livewire.charts.persentase-distribusi-chart');
    }
}
