<?php

namespace App\Http\Controllers;


use App\Models\Project;
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

        $query = Project::select(['id', 'project_code', 'name']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('project_code', 'LIKE', "%{$search}%")
                    ->orWhere('name', 'LIKE', "%{$search}%");
            });
        }

        $perPage = (int) $request->get('per_page', 10);
        $projects = $query->paginate($perPage);

        return response()->json($projects);
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
