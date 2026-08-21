<?php

namespace App\Http\Controllers;


use App\Models\Project;
use App\Models\Oka;
use Illuminate\Http\Request;

class ProjectCodeController extends Controller
{
    public function index(Request $request){
        return view('project-code.index');
    }

    public function getData(Request $request){
        if (! $request->ajax()) {
            abort(404);
        }

        $query = Project::with('okas')->select(['id', 'project_code', 'name']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('project_code', 'LIKE', "%{$search}%")
                    ->orWhere('name', 'LIKE', "%{$search}%");
            });
        }

        $perPage = (int) $request->get('per_page', 10);
        $projects = $query->paginate($perPage);

        $projects->setCollection(
            $projects->getCollection()->map(function ($project) {
                $oka = $project->okas->first();

                return [
                    'id' => $project->id,
                    'project_code' => $project->project_code,
                    'name' => $project->name,
                    'oka' => $oka?->oka,
                    'wbs' => $oka?->wbs,
                    'label_proyek' => $oka?->label_proyek,
                    'total_car' => $oka?->total_car,
                    'jig' => $oka?->jig,
                    'prototype' => $oka?->prototype,
                    'tgl_oka' => $oka?->tgl_oka?->format('d-m-Y'),
                ];
            })
        );

        return response()->json($projects);
    }

    public function edit($id){
        $project = Project::with('okas')->findOrFail($id);

        return view('project-code.edit', compact('project'));
    }

    public function update(Request $request, $id){
        $project = Project::findOrFail($id);

        $validated = $request->validate([
            'project_code' => 'required|unique:projects,project_code,' . $id,
            'name' => 'nullable',
            'oka' => 'nullable',
            'wbs' => 'nullable',
            'label_proyek' => 'nullable',
            'total_car' => 'nullable|integer',
            'jig' => 'nullable',
            'prototype' => 'nullable',
            'tgl_oka' => 'nullable|date',
        ]);

        $project->update([
            'project_code' => $validated['project_code'],
            'name' => $validated['name'],
        ]);

        $oka = $project->okas()->first();

        $okaData = [
            'oka' => $validated['oka'],
            'wbs' => $validated['wbs'],
            'label_proyek' => $validated['label_proyek'],
            'total_car' => $validated['total_car'],
            'jig' => $validated['jig'],
            'prototype' => $validated['prototype'],
            'tgl_oka' => $validated['tgl_oka'],
        ];

        if ($oka) {
            $oka->update($okaData);
        } else {
            Oka::create(array_merge(['project_id' => $project->id], $okaData));
        }

        return redirect()->route('project-code.index')
            ->with('success', 'Data project berhasil diperbarui.');
    }

    public function create(Request $request){
        $validated = $request->validate([
            'project_code' => 'required|unique:projects,project_code',
            'name' => 'required',
        ]);

        try {
            $project = Project::create([
                'project_code' => $validated['project_code'],
                'name' => $validated['name'],
                'status' => true,
            ]);
    
            return response()->json(['message' => 'Project created successfully', 'project' => $project], 201);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Failed to create project', 'error' => $th->getMessage()], 500);
        }
    }
}
