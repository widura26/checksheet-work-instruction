<?php

namespace App\Http\Controllers;

use App\Models\BillOfMaterial;
use App\Models\BillOfMaterialRevision;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BomController extends Controller
{
    public function index(Request $request){
        return view('bill-of-material.index');
    }

    public function getData(Request $request){
        if (! $request->ajax()) {
            abort(404);
        }

        $query = BillOfMaterial::select('bill_of_materials.*')
            ->leftJoin('projects', 'projects.id', '=', 'bill_of_materials.project_id')
            ->select(
                'bill_of_materials.id',
                'bill_of_materials.number',
                'bill_of_materials.name',
                'bill_of_materials.published_date',
                'projects.project_id as project_code',
                'projects.name_project'
            )->with('latestRevision');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('bill_of_materials.number', 'LIKE', "%{$search}%")
                    ->orWhere('bill_of_materials.name', 'LIKE', "%{$search}%")
                    ->orWhere('projects.project_id', 'LIKE', "%{$search}%")
                    ->orWhere('projects.name_project', 'LIKE', "%{$search}%");
            });
        }

        $perPage = (int) $request->get('per_page', 10);
        $bom = $query->orderBy('bill_of_materials.created_at', 'desc')->paginate($perPage);

        $bom->setCollection(
            $bom->getCollection()->map(function ($item) {
                return [
                    'id'             => $item->id,
                    'number'         => $item->number,
                    'name'           => $item->name,
                    'published_date' => $item->published_date ? \Carbon\Carbon::parse($item->published_date)->format('d-m-Y') : null,
                    'revise'         => $item->latestRevision->revision_version,
                    'project_code'   => $item->project_code,
                    'project_name'   => $item->name_project,
                ];
            })
        );

        return response()->json($bom);
    }

    public function create(){
        $projects = Project::orderBy('project_id')->get();
        return view('bill-of-material.create', compact('projects'));
    }

    public function store(Request $request){
        $validated = $request->validate([
            'project_id'         => 'required',
            'number'             => 'required|string|max:255',
            'name'               => 'required|string|max:255',
            'published_date'     => 'required|date',
            'documents'          => 'required|array',
            'documents.*'        => 'file|mimes:pdf,xls,xlsx|max:5120',
            'revision_versions'  => 'nullable|array',
            'revision_versions.*'=> 'nullable|string|max:255',
        ]);

        $bom = BillOfMaterial::create([
            'project_id'     => $validated['project_id'],
            'number'         => $validated['number'],
            'name'           => $validated['name'],
            'published_date' => $validated['published_date'],
        ]);

        if ($request->hasFile('documents')) {
            $files = $request->file('documents');
            $versions = $request->input('revision_versions', []);
            foreach ($files as $index => $file) {
                $path = $file->store('bill-of-material-revisions', 'public');
                BillOfMaterialRevision::create([
                    'bom_id'            => $bom->id,
                    'original_name_file'=> $file->getClientOriginalName(),
                    'path_file'         => $path,
                    'revision_version'  => $versions[$index] ?? '0',
                ]);
            }
        }

        return redirect()->route('bill-of-material.index')->with('success', 'Bill Of Material berhasil ditambahkan.');
    }

    public function edit(string $id){
        $bom = BillOfMaterial::with('revisions')->findOrFail($id);
        $projects = Project::orderBy('project_id')->get();

        return view('bill-of-material.edit', compact('bom', 'projects'));
    }

    public function update(Request $request, string $id){
        $bom = BillOfMaterial::findOrFail($id);

        $validated = $request->validate([
            'project_id'         => 'required',
            'number'             => 'required|string|max:255',
            'name'               => 'required|string|max:255',
            'published_date'     => 'required|date',
            'documents'          => 'nullable|array',
            'documents.*'        => 'file|mimes:pdf,xls,xlsx|max:5120',
            'revision_versions'  => 'nullable|array',
            'revision_versions.*'=> 'nullable|string|max:255',
        ]);

        $bom->update([
            'project_id'     => $validated['project_id'],
            'number'         => $validated['number'],
            'name'           => $validated['name'],
            'published_date' => $validated['published_date'],
        ]);

        if ($request->hasFile('documents')) {
            $files = $request->file('documents');
            $versions = $request->input('revision_versions', []);
            foreach ($files as $index => $file) {
                $path = $file->store('bill-of-material-revisions', 'public');
                BillOfMaterialRevision::create([
                    'bom_id'            => $bom->id,
                    'original_name_file'=> $file->getClientOriginalName(),
                    'path_file'         => $path,
                    'revision_version'  => $versions[$index] ?? '0',
                ]);
            }
        }

        return redirect()->route('bill-of-material.index')->with('success', 'Bill Of Material berhasil diperbarui.');
    }

    public function delete(string $id){
        $bom = BillOfMaterial::with('revisions')->findOrFail($id);

        foreach ($bom->revisions as $revision) {
            Storage::disk('public')->delete($revision->path_file);
            $revision->delete();
        }

        $bom->delete();

        return redirect()->route('bill-of-material.index')->with('success', 'Bill Of Material berhasil dihapus.');
    }
}
