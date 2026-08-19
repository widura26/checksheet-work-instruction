<?php

namespace App\Livewire\Charts;

use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\Attributes\On;

class DistribusiPerProjectChart extends Component
{
    public $data;
    public $selectedYear;
    public $selectedProject;
    public function mount($selectedProject = null, $selectedYear = null)
    {
        $this->selectedYear = $selectedYear;
        $this->selectedProject = $selectedProject;
        $this->getData();
        $this->dispatch('projectUpdatedDistribusiChart', data: $this->data);
    }
    #[On('selectedProjectChanged')]
    public function updateProjectFromParent($projectId)
    {
        $this->selectedProject = $projectId;
        $this->getData();
        $this->dispatch('projectUpdatedDistribusiChart', data: $this->data);
    }
    #[On('selectedYearChanged')]
    public function selectedYearChanged($year)
    {
        $this->selectedYear = $year;
        $this->getData();
        $this->dispatch('projectUpdatedDistribusiChart', data: $this->data);
    }
    private function getData()
    {
        $tahun = $this->selectedYear ?? date('Y');
        $projects = DB::table('projects')
            ->join('distribusi_tools', 'projects.project_id', '=', 'distribusi_tools.project_id')
            ->join('daftar_distribusi_tools', 'distribusi_tools.id', '=', 'daftar_distribusi_tools.distribusi_tools_id')
            ->whereYear('distribusi_tools.tanggal_pinjam_diserahkan', $tahun)
            ->when($this->selectedProject, function ($query) {
                return $query->where('projects.project_id', $this->selectedProject);
            })
            ->whereNull('projects.closed_at')
            ->whereNot('projects.project_id', 'UU')
            ->select('projects.project_id', DB::raw('COUNT(daftar_distribusi_tools.id) as total_distribusi'))
            ->orderBy('project_id', 'desc')
            ->groupBy('projects.project_id')
            ->get();
        $data = $projects->map(function ($item) {
            return [
                'project_id' => $item->project_id,
                'total_distribusi' => $item->total_distribusi,
            ];
        });
        $this->data = [
            'project_id' => $data->pluck('project_id'),
            'total_distribusi' => $data->pluck('total_distribusi'),
        ];
    }
    public function render()
    {
        return view('livewire.charts.distribusi-per-project-chart');
    }
}
