<?php

namespace App\Livewire\Charts;

use App\Models\Tools;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\Attributes\On;

class StokRendahPerProjectChart extends Component
{
    public $data;
    public $selectedProject;
    public function mount($selectedProject = null)
    {
        $this->selectedProject = $selectedProject;
    }
    #[On('selectedProjectChanged')]
    public function updateProjectFromParent($projectId)
    {
        $this->selectedProject = $projectId;
        $this->getData();
        $this->dispatch('projectUpdatedStokRendahPerProjectChart', data: $this->data);
    }
    private function getData()
    {
        $lowStockThreshold = DB::table('kode_bagian_spm')->first()->stok_rendah;
        $totalTools = Tools::where('jenis_material', 'tool')->whereHas('projectStocks', function ($query) use ($lowStockThreshold) {
            $query->where('project_id', $this->selectedProject)
                ->where('qty', '<', $lowStockThreshold);
        });
        $totalConsumables = Tools::where('jenis_material', 'consumable')->whereHas('projectStocks', function ($query) use ($lowStockThreshold) {
            $query->where('project_id', $this->selectedProject)
                ->where('qty', '<', $lowStockThreshold);
        });
        $totalApd = Tools::where('jenis_material', 'apd')->whereHas('projectStocks', function ($query) use ($lowStockThreshold) {
            $query->where('project_id', $this->selectedProject)
                ->where('qty', '<', $lowStockThreshold);
        });

        $this->data = [
            'tool' => $totalTools->count(),
            'consumable' => $totalConsumables->count(),
            'apd' => $totalApd->count(),
        ];
    }
    public function render()
    {
        return view('livewire.charts.stok-rendah-per-project-chart');
    }
}
