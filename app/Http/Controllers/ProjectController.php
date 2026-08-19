<?php

namespace App\Http\Controllers;

use App\Imports\ProjectImport;
use App\Models\Posisi_CT;
use App\Models\Project;
use App\Models\ToolProjectStock;
use App\Models\Tools;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class ProjectController extends Controller
{
    public function __construct() {}

    public function index()
    {
        return view('pages.projects.index');
    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $projects = Project::withoutGlobalScope('onlyWithStock');

            return DataTables::of($projects)
                ->addIndexColumn()
                ->addColumn('action', function ($project) {
                    if (!Auth::user()->hasRole('admin')) {
                        return '-';
                    }
                    $btn = '';
                    if ($project->projectTools()->exists()) {
                        $btn .= ' <a href="' . route('projects.show', $project) . '" class="btn btn-sm btn-info text-white">Detail</a>';
                        if ($project->closed_at === null) {
                            if ($project->project_id !== 'UU') {
                                $btn .= '<form action="' . route('projects.close', $project) . '" 
                                method="POST" 
                                class="d-inline form-close-project">
                            ' . csrf_field() . '
                            <button type="button" 
                                    class="btn btn-sm btn-danger text-white btn-close-project">
                                Tutup Project
                            </button>
                        </form>';
                                $btn .= '</div>';
                            }
                        } else {
                            $btn .= '<button class="btn btn-sm bg-primary text-white ms-2">Project Ditutup</button>';
                        }
                    }

                    return $btn;
                })
                ->make(true);
        }
    }

    public function create()
    {
        abort(404);

        return view('pages.projects.create');
    }

    public function store(Request $request)
    {
        abort(404);
        $request->validate([
            'project_id' => 'required|string|max:50|unique:projects,project_id',
            'nama_project' => 'required|string|max:100|unique:projects,nama_project',
        ]);

        Project::create($request->all());

        return redirect()->route('projects.index')->with('success', 'Project created successfully.');
    }

    public function show(Project $project)
    {
        return view('pages.projects.show', compact('project'));
    }

    public function getDataTools(Request $request, Project $project)
    {
        if ($request->ajax()) {
            $tools = ToolProjectStock::where('project_id', $project->project_id)->with(['tool.posisiCT']);

            return DataTables::of($tools)
                ->addIndexColumn()
                ->addColumn('kode_material', function ($toolProjectStock) {
                    return $toolProjectStock->tool->kode_material ?? '-';
                })
                ->editColumn('qty', function ($toolProjectStock) {
                    return $toolProjectStock->qty_formatted ?? '-';
                })
                ->addColumn('spesifikasi', function ($toolProjectStock) {
                    return $toolProjectStock->tool->spesifikasi ?? '-';
                })
                ->addColumn('deskripsi', function ($toolProjectStock) {
                    return $toolProjectStock->tool->deskripsi ?? '-';
                })
                ->addColumn('posisi', function ($toolProjectStock) {
                    return $toolProjectStock->tool->posisiCT->nama_posisi_ct ?? '-';
                })
                ->make(true);
        }
    }

    public function edit(Project $project)
    {
        abort(404);

        return view('pages.projects.edit', compact('project'));
    }

    public function update(Request $request, Project $project)
    {
        abort(404);
    }

    public function import(Request $request)
    {
        try {
            DB::beginTransaction();
            $request->validate([
                'excel_file' => [
                    'required',
                    'mimes:xlsx,xls,csv',
                    'max:3048',
                ],
            ], [
                'excel_file.required' => 'File harus diisi.',
                'excel_file.mimes' => 'File harus berformat XLSX, XLS, atau CSV.',
                'excel_file.max' => 'File maksimal 3MB.',
            ]);
            Excel::import(new ProjectImport, $request->file('excel_file'));
            DB::commit();

            return redirect()->route('projects.index')->with('success', 'Import Project berhasil.');
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error('Database error during project import: ' . $e->getMessage(), ['user_id' => Auth::id()]);
            return redirect()->back()->with('error', 'Gagal mengimport data: Terjadi kesalahan pada database.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Database error during project import: ' . $e->getMessage(), ['user_id' => Auth::id()]);
            return redirect()->back()->with('error', 'Gagal mengimport data');
        }
    }
    public function tutupProject(Project $project)
    {
        if ($project->closed_at !== null) {
            abort(403, 'Project sudah ditutup.');
        }
        DB::beginTransaction();
        try {
            $project->load('projectTools');
            // pindah stock project yang di tutup ke stok UU
            $ctUU = Posisi_CT::whereNull('departemen_id')->first()->id;
            foreach ($project->projectTools as $toolStock) {
                if ($toolStock->pivot->qty <= 0) {
                    continue;
                }
                // cari project UU yang memiliki stock tool yang sama dengan posisi_ct_id yang sama
                $material = Tools::where('kode_material', $toolStock->kode_material)->where('posisi_ct_id', $ctUU)->first() ?? null;
                if ($material) {
                    $stock = ToolProjectStock::where('project_id', 'UU')
                        ->where('tool_id', $material->id)
                        ->where('posisi_ct_id', $ctUU)
                        ->first();
                    if ($stock) {
                        $stock->increment('qty', $toolStock->pivot->qty);
                        // set stock project yang di tutup menjadi 0
                        DB::table('tool_project_stocks')->where('project_id', $project->project_id)
                            ->where('tool_id', $toolStock->id)
                            ->where('posisi_ct_id', $toolStock->posisi_ct_id)
                            ->update(['qty' => 0]);
                    } else {
                        DB::table('tool_project_stocks')->insert(
                            [
                                'project_id' => 'UU',
                                'tool_id' => $material->id,
                                'qty' => $toolStock->pivot->qty,
                                'posisi_ct_id' => $ctUU,
                            ]
                        );
                        // set stock project yang di tutup menjadi 0
                        DB::table('tool_project_stocks')->where('project_id', $project->project_id)
                            ->where('tool_id', $toolStock->id)
                            ->where('posisi_ct_id', $toolStock->pivot->posisi_ct_id)
                            ->update(['qty' => 0]);
                    }
                } else {
                    $newMaterial = Tools::create([
                        'public_id' => Str::uuid(),
                        'kode_material' => $toolStock->kode_material,
                        'deskripsi' => $toolStock->deskripsi,
                        'spesifikasi' => $toolStock->spesifikasi,
                        'satuan' => $toolStock->satuan,
                        'vendor' => $toolStock->vendor,
                        'keterangan_vendor' => 'Material Dari Penutupan Project ' . $project->nama_project,
                        'jenis_material' => $toolStock->jenis_material,
                        'tanggal_ditambahkan' => now(),
                        'posisi_ct_id' => $ctUU,
                    ]);
                    DB::table('tool_project_stocks')->insert(
                        [
                            'project_id' => 'UU',
                            'tool_id' => $newMaterial->id,
                            'qty' => $toolStock->pivot->qty,
                            'posisi_ct_id' => $ctUU,
                        ]
                    );
                    // set stock project yang di tutup menjadi 0
                    DB::table('tool_project_stocks')->where('project_id', $project->project_id)
                        ->where('tool_id', $toolStock->id)
                        ->where('posisi_ct_id', $toolStock->pivot->posisi_ct_id)
                        ->update(['qty' => 0]);
                }
            }
            $project->closed_at = now();
            $project->save();
            DB::commit();
            return redirect()->route('projects.index')->with('success', 'Project Berhasil Ditutup.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error closing project: ' . $e->getMessage(), ['project_id' => $project->project_id, 'user_id' => Auth::id()]);
            return redirect()->back()->with('error', 'Gagal Menutup Project');
        }
    }
    public function destroy(Project $project)
    {
        $project->delete();

        return redirect()->route('projects.index')->with('success', 'Project deleted successfully.');
    }
}
