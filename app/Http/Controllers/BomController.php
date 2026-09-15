<?php

namespace App\Http\Controllers;

use App\Models\BillOfMaterial;
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
        ]);

        BillOfMaterial::create($validated);

        return redirect()->route('bill-of-material.index')->with('success', 'Bill Of Material berhasil ditambahkan.');
    }
}
