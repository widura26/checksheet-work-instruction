<?php

namespace App\Livewire\Charts;

use App\Models\Tools;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\Attributes\On;

class StokRendahKategoriChart extends Component
{
    public $data;
    public $selectedProject;
    public function mount($selectedProject = null)
    {
        $this->selectedProject = $selectedProject;
        $this->getData();
        $this->dispatch('projectUpdatedStokRendahChart', data: $this->data);
    }

    #[On('selectedProjectChanged')]
    public function updateProjectFromParent($projectId)
    {
        $this->selectedProject = $projectId;
        $this->getData();
        $this->dispatch('projectUpdatedStokRendahChart', data: $this->data);
    }
    private function getData()
    {
        $lowStockThreshold = DB::table('kode_bagian_spm')->first()->stok_rendah;

        $query = DB::table('tool_project_stocks')
            ->join('tools', 'tools.id', '=', 'tool_project_stocks.tool_id')
            ->join('projects', 'projects.project_id', '=', 'tool_project_stocks.project_id')
            ->when($this->selectedProject, function ($query) {
                $query->where('tool_project_stocks.project_id', $this->selectedProject);
            })
            ->whereNull('projects.closed_at')
            ->whereNot('projects.project_id', 'UU')
            ->where('tool_project_stocks.qty', '<', $lowStockThreshold)
            ->select(
                'projects.project_id',
                'projects.nama_project',
                'tools.jenis_material',
                DB::raw('COUNT(*) as total')
            )
            ->orderBy('projects.project_id', 'desc')
            ->groupBy('projects.project_id', 'projects.nama_project', 'tools.jenis_material')
            ->get();
        $formatted = [];

        foreach ($query as $row) {
            $projectId = $row->project_id;

            if (!isset($formatted[$projectId])) {
                $formatted[$projectId] = [
                    'nama_project' => $row->nama_project,
                    'tool' => 0,
                    'consumable' => 0,
                    'apd' => 0,
                ];
            }

            $formatted[$projectId][$row->jenis_material] = $row->total;
        }
        $this->data = array_values($formatted);
    }
    public function render()
    {
        return view('livewire.charts.stok-rendah-kategori-chart');
    }
}
