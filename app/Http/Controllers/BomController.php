<?php

namespace App\Http\Controllers;

use App\Models\BillOfMaterial;
use App\Models\BillOfMaterialRevision;
use App\Models\Project;
use Illuminate\Http\Request;

class BomController extends Controller
{
    public function index(Request $request){
        return view('bill-of-material.index');
    }

    public function create(){
        $projects = Project::orderBy('project_id')->get();
        return view('bill-of-material.create', compact('projects'));
    }

    public function store(Request $request){
        $validated = $request->validate([
            'project_id'      => 'required',
            'number'          => 'required|string|max:255',
            'name'            => 'required|string|max:255',
            'published_date'  => 'required|date',
            'revise'          => 'nullable|string|max:255',
            'documents'       => 'nullable|array',
            'documents.*'     => 'file|mimes:pdf,doc,docx,xls,xlsx,png,jpg,jpeg|max:5120',
        ]);

        $bom = BillOfMaterial::create([
            'project_id'     => $validated['project_id'],
            'number'         => $validated['number'],
            'name'           => $validated['name'],
            'published_date' => $validated['published_date'],
            'revise'         => $validated['revise'] ?? '0',
        ]);

        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $file) {
                $path = $file->store('bill-of-material-revisions', 'public');
                BillOfMaterialRevision::create([
                    'bom_id'     => $bom->id,
                    'path_file'  => $path,
                ]);
            }
        }

        return redirect()->route('bill-of-material.index')->with('success', 'Bill Of Material berhasil ditambahkan.');
    }
}
