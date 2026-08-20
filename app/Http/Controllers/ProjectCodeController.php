<?php

namespace App\Http\Controllers;


use App\Models\Project;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;

class ProjectCodeController extends Controller
{
    public function index(Request $request){
        return view('project-code.index');
    }

    public function data(Request $request){
        if (! $request->ajax()) {
            abort(404);
        }

        $query = Project::select(['id', 'project_code', 'name', 'status']);

        if ($request->filled('project_code')) {
            $query->where('project_code', 'LIKE', '%' . $request->project_code . '%');
        }

        if ($request->filled('nama')) {
            $query->where('nama', 'LIKE', '%' . $request->nama . '%');
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->make(true);
    }
}
