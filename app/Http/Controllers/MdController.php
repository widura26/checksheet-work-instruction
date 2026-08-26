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
        $projects = Project::orderBy('project_id')->get(['id', 'project_id']);
        $okas = Oka::orderBy('oka')->get(['id', 'oka']);

        return view('md.index', compact('projects', 'okas'));
    }

    public function data(Request $request)
    {
        if (! $request->ajax()) {
            abort(404);
        }

        $query = Mdr::select([
            'id',
            'id_mdr',
            'project_id',
            'oka_id',
            'tgl_mdr',
            'masalah',
            'lanjut',
            'input',
            'dampak',
        ]);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id_mdr', 'LIKE', "%{$search}%")
                    ->orWhere('masalah', 'LIKE', "%{$search}%")
                    ->orWhere('lanjut', 'LIKE', "%{$search}%")
                    ->orWhere('input', 'LIKE', "%{$search}%")
                    ->orWhere('dampak', 'LIKE', "%{$search}%");
            });
        }

        $perPage = (int) $request->get('per_page', 10);
        $mdrs = $query->paginate($perPage);

        return response()->json($mdrs);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_mdr' => 'required|string|max:255|unique:mdrs,id_mdr',
            'project_id' => 'nullable|exists:projects,id',
            'oka_id' => 'nullable|exists:okas,id',
            'tgl_mdr' => 'nullable|date',
            'masalah' => 'nullable|string',
            'lanjut' => 'nullable|string|max:255',
            'input' => 'nullable|string|max:255',
            'dampak' => 'nullable|string|max:255',
        ], [
            'id_mdr.required' => 'ID MDR wajib diisi.',
            'id_mdr.unique' => 'ID MDR sudah digunakan.',
            'project_id.exists' => 'Proyek tidak ditemukan.',
            'oka_id.exists' => 'OKA tidak ditemukan.',
            'tgl_mdr.date' => 'Tanggal MDR harus berupa tanggal.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        Mdr::create([
            'id_mdr' => $request->id_mdr,
            'project_id' => $request->project_id ?: null,
            'oka_id' => $request->oka_id ?: null,
            'tgl_mdr' => $request->tgl_mdr ?: null,
            'masalah' => $request->masalah,
            'lanjut' => $request->lanjut,
            'input' => $request->input,
            'dampak' => $request->dampak,
            'user_id' => Auth::id(),
        ]);

        return response()->json(['success' => true, 'message' => 'Data berhasil ditambahkan.']);
    }

    public function update(Request $request, Mdr $mdr)
    {
        $validator = Validator::make($request->all(), [
            'id_mdr' => 'required|string|max:255|unique:mdrs,id_mdr,' . $mdr->id_mdr . ',id_mdr',
            'project_id' => 'nullable|exists:projects,id',
            'oka_id' => 'nullable|exists:okas,id',
            'tgl_mdr' => 'nullable|date',
            'masalah' => 'nullable|string',
            'lanjut' => 'nullable|string|max:255',
            'input' => 'nullable|string|max:255',
            'dampak' => 'nullable|string|max:255',
        ], [
            'id_mdr.required' => 'ID MDR wajib diisi.',
            'id_mdr.unique' => 'ID MDR sudah digunakan.',
            'project_id.exists' => 'Proyek tidak ditemukan.',
            'oka_id.exists' => 'OKA tidak ditemukan.',
            'tgl_mdr.date' => 'Tanggal MDR harus berupa tanggal.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $mdr->update([
            'id_mdr' => $request->id_mdr,
            'project_id' => $request->project_id ?: null,
            'oka_id' => $request->oka_id ?: null,
            'tgl_mdr' => $request->tgl_mdr ?: null,
            'masalah' => $request->masalah,
            'lanjut' => $request->lanjut,
            'input' => $request->input,
            'dampak' => $request->dampak,
        ]);

        return response()->json(['success' => true, 'message' => 'Data berhasil diperbarui.']);
    }

    public function destroy(Mdr $mdr)
    {
        $mdr->delete();

        return response()->json(['success' => true, 'message' => 'Data berhasil dihapus.']);
    }
}
