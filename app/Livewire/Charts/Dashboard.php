<?php

namespace App\Livewire\Charts;

use App\Models\Project;
use Livewire\Component;

class Dashboard extends Component
{
    public $projects;
    public $selectedProject;
    public $selectedYear;
    public $years = [];

    public function mount()
    {
        $this->projects = Project::whereNull('closed_at')->where('project_id', '<>', 'UU')->get();
        $this->selectedProject = null;
        $this->years = range(now()->year, now()->year - 2);
        $this->selectedYear = now()->year;
    }
    public function updatedSelectedProject($value)
    {
        $this->dispatch('selectedProjectChanged', projectId: $value);
    }
    public function updatedSelectedYear($value)
    {
        $this->dispatch('selectedYearChanged', year: $value);
    }
    public function render()
    {
        return view('livewire.charts.dashboard', ['projects' => $this->projects]);
    }
}
