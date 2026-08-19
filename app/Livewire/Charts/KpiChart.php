<?php

namespace App\Livewire\Charts;

use App\Models\ToolProjectStock;
use App\Models\Tools;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Component;

class KpiChart extends Component
{
    public $projects;
    public $selectedProject;
    public $totalTool;
    public $totalConsumable;
    public $totalApd;
    public $totalStokRendah;
    public $lowStockThreshold;
    public function mount($selectedProject = null)
    {
        $this->selectedProject = $selectedProject;
        $this->lowStockThreshold = DB::table('kode_bagian_spm')->first()->stok_rendah;
    }
    #[On('selectedProjectChanged')]
    public function updateProjectFromParent($projectId)
    {
        $this->selectedProject = $projectId;
        $this->getData();
    }
    private function getData()
    {
        $this->totalTool = Tools::where('jenis_material', 'tool')->whereHas('projectStocks', function ($query) {
            $query->where('project_id', $this->selectedProject);
        })->count();
        $this->totalConsumable = Tools::where('jenis_material', 'consumable')->whereHas('projectStocks', function ($query) {
            $query->where('project_id', $this->selectedProject);
        })->count();
        $this->totalApd = Tools::where('jenis_material', 'apd')->whereHas('projectStocks', function ($query) {
            $query->where('project_id', $this->selectedProject);
        })->count();
        $this->totalStokRendah = ToolProjectStock::where('qty', '<', $this->lowStockThreshold)
            ->where('project_id', $this->selectedProject)
            ->whereHas('tool')
            ->count();
    }
    public function render()
    {
        return view('livewire.charts.kpi-chart', ['projects' => $this->projects]);
    }
}
