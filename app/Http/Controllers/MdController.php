<?php

namespace App\Http\Controllers;

use App\Models\Mdr;
use App\Models\Oka;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class MdController extends Controller
{
    public function index()
    {
        $projects = Project::orderBy('project_id')->get(['id', 'project_id', 'name_project']);
        $okas = Oka::orderBy('oka')->get(['id', 'oka']);

        return view('md.index', compact('projects', 'okas'));
    }

    public function data(Request $request)
    {
        if (! $request->ajax()) {
            abort(404);
        }

        $query = Mdr::query()->leftJoin('projects', 'manufacture_drawings.project_id', '=', 'projects.id')
        ->select([
            'manufacture_drawings.id',
            'manufacture_drawings.manufacture_drawing_code',
            'projects.name_project as name_project',
            'manufacture_drawings.manufacture_drawing_date',
            'manufacture_drawings.description',
        ]);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('manufacture_drawing_code', 'LIKE', "%{$search}%")
                    ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        $perPage = (int) $request->get('per_page', 10);
        $mdrs = $query->paginate($perPage);

        return response()->json($mdrs);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_mdr' => 'required|string|max:255|unique:manufacture_drawings,manufacture_drawing_code',
            'project_id' => 'nullable|exists:projects,id',
            'tgl_mdr' => 'nullable|date',
            'masalah' => 'nullable|string',
        ], [
            'id_mdr.required' => 'Manufacture Drawing Code wajib diisi.',
            'id_mdr.unique' => 'Manufacture Drawing Code sudah digunakan.',
            'project_id.exists' => 'Proyek tidak ditemukan.',
            'tgl_mdr.date' => 'Tanggal harus berupa tanggal.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        Mdr::create([
            'manufacture_drawing_code' => $request->id_mdr,
            'project_id' => $request->project_id ?: null,
            'manufacture_drawing_date' => $request->tgl_mdr ?: null,
            'user_id' => Auth::id(),
            'description' => $request->masalah,
        ]);

        return response()->json(['success' => true, 'message' => 'Data berhasil ditambahkan.']);
    }

    public function update(Request $request, Mdr $mdr)
    {
        $validator = Validator::make($request->all(), [
            'id_mdr' => 'required|string|max:255|unique:manufacture_drawings,manufacture_drawing_code,' . $mdr->manufacture_drawing_code . ',manufacture_drawing_code',
            'project_id' => 'nullable|exists:projects,id',
            'tgl_mdr' => 'nullable|date',
            'masalah' => 'nullable|string',
        ], [
            'id_mdr.required' => 'Manufacture Drawing Code wajib diisi.',
            'id_mdr.unique' => 'Manufacture Drawing Code sudah digunakan.',
            'project_id.exists' => 'Proyek tidak ditemukan.',
            'tgl_mdr.date' => 'Tanggal harus berupa tanggal.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $mdr->update([
            'manufacture_drawing_code' => $request->id_mdr,
            'project_id' => $request->project_id ?: null,
            'manufacture_drawing_date' => $request->tgl_mdr ?: null,
            'description' => $request->masalah,
        ]);

        return response()->json(['success' => true, 'message' => 'Data berhasil diperbarui.']);
    }

    public function destroy(Mdr $mdr)
    {
        $mdr->delete();

        return response()->json(['success' => true, 'message' => 'Data berhasil dihapus.']);
    }
}
