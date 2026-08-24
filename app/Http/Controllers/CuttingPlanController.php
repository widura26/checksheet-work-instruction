<?php

namespace App\Http\Controllers;

use App\Models\CuttingPlan;
use App\Models\Material;
use App\Models\Project;
use App\Models\Proses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CuttingPlanController extends Controller
{
    public function index()
    {
        $projects = Project::all();
        $processes = Proses::all();
        return view('cutting-plan.index', compact('projects', 'processes'));
    }

    private function normalizeEmpty(Request $request)
    {
        $data = $request->all();
        array_walk_recursive($data, function (&$value) {
            if ($value === '') {
                $value = null;
            }
        });
        $request->merge($data);
    }

    public function getData(Request $request)
    {
        if (! $request->ajax()) {
            abort(404);
        }

        $query = CuttingPlan::select([
            'id', 'id_cp', 'no_cp', 'mat', 'spec_mat', 'mat_sisa',
            'shop', 'diterima', 'tgl_kirim', 'grup', 'lot', 'qty_lot',
            'material_size', 'quantity', 'wbs', 'sketch', 'cutting_method',
            'special_instruction', 'date_issued', 'date_revised', 'kode_proyek',
            'prepared_by', 'approved_by', 'date_operator', 'bpm_number', 'date_of_bpm',
            'pieceparts',
        ]);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id_cp', 'LIKE', "%{$search}%")
                    ->orWhere('no_cp', 'LIKE', "%{$search}%")
                    ->orWhere('mat', 'LIKE', "%{$search}%")
                    ->orWhere('spec_mat', 'LIKE', "%{$search}%")
                    ->orWhere('shop', 'LIKE', "%{$search}%")
                    ->orWhere('grup', 'LIKE', "%{$search}%")
                    ->orWhere('lot', 'LIKE', "%{$search}%");
            });
        }

        $perPage = (int) $request->get('per_page', 10);
        $cps = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json($cps);
    }

    public function store(Request $request)
    {
        $this->normalizeEmpty($request);

        $validator = Validator::make($request->all(), [
            'id_cp' => 'nullable|string|max:255',
            'no_cp' => 'required|string|max:255|unique:cutting_plans,no_cp',
            'mat' => 'nullable|string|max:255',
            'spec_mat' => 'nullable|string|max:255',
            'mat_sisa' => 'nullable|string|max:255',
            'shop' => 'nullable|string|max:255',
            'diterima' => 'nullable|string|max:255',
            'tgl_kirim' => 'nullable|date',
            'grup' => 'nullable|string|max:255',
            'lot' => 'nullable|string|max:255',
            'qty_lot' => 'nullable|integer',
            'material_size' => 'nullable|string|max:255',
            'quantity' => 'nullable|integer',
            'wbs' => 'nullable|string|max:255',
            'sketch' => 'nullable|string|max:255',
            'cutting_method' => 'nullable|string|max:255',
            'special_instruction' => 'nullable|string',
            'date_issued' => 'nullable|date',
            'date_revised' => 'nullable|date',
            'kode_proyek' => 'nullable|string|max:255',
            'prepared_by' => 'nullable|string|max:255',
            'approved_by' => 'nullable|string|max:255',
            'date_operator' => 'nullable|string|max:255',
            'bpm_number' => 'nullable|string|max:255',
            'date_of_bpm' => 'nullable|date',
            'pieceparts' => 'nullable|array',
            'pieceparts.*.part_no' => 'nullable|string|max:255',
            'pieceparts.*.cutting_size' => 'nullable|string|max:255',
            'pieceparts.*.qty_car' => 'nullable|string|max:255',
            'pieceparts.*.qty_lot' => 'nullable|string|max:255',
            'pieceparts.*.part_name' => 'nullable|string|max:255',
            'pieceparts.*.remark' => 'nullable|string|max:255',
        ], [
            'no_cp.required' => 'No CP wajib diisi.',
            'no_cp.unique' => 'No CP sudah digunakan.',
            'quantity.integer' => 'Quantity harus berupa angka.',
            'qty_lot.integer' => 'Qty Lot harus berupa angka.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        CuttingPlan::create($validator->validated());

        return response()->json(['success' => true, 'message' => 'Data berhasil ditambahkan.']);
    }

    public function update(Request $request, CuttingPlan $cuttingPlan)
    {
        $this->normalizeEmpty($request);

        $validator = Validator::make($request->all(), [
            'id_cp' => 'nullable|string|max:255',
            'no_cp' => 'required|string|max:255|unique:cutting_plans,no_cp,' . $cuttingPlan->id . ',id',
            'mat' => 'nullable|string|max:255',
            'spec_mat' => 'nullable|string|max:255',
            'mat_sisa' => 'nullable|string|max:255',
            'shop' => 'nullable|string|max:255',
            'diterima' => 'nullable|string|max:255',
            'tgl_kirim' => 'nullable|date',
            'grup' => 'nullable|string|max:255',
            'lot' => 'nullable|string|max:255',
            'qty_lot' => 'nullable|integer',
            'material_size' => 'nullable|string|max:255',
            'quantity' => 'nullable|integer',
            'wbs' => 'nullable|string|max:255',
            'sketch' => 'nullable|string|max:255',
            'cutting_method' => 'nullable|string|max:255',
            'special_instruction' => 'nullable|string',
            'date_issued' => 'nullable|date',
            'date_revised' => 'nullable|date',
            'kode_proyek' => 'nullable|string|max:255',
            'prepared_by' => 'nullable|string|max:255',
            'approved_by' => 'nullable|string|max:255',
            'date_operator' => 'nullable|string|max:255',
            'bpm_number' => 'nullable|string|max:255',
            'date_of_bpm' => 'nullable|date',
            'pieceparts' => 'nullable|array',
            'pieceparts.*.part_no' => 'nullable|string|max:255',
            'pieceparts.*.cutting_size' => 'nullable|string|max:255',
            'pieceparts.*.qty_car' => 'nullable|string|max:255',
            'pieceparts.*.qty_lot' => 'nullable|string|max:255',
            'pieceparts.*.part_name' => 'nullable|string|max:255',
            'pieceparts.*.remark' => 'nullable|string|max:255',
        ], [
            'no_cp.required' => 'No CP wajib diisi.',
            'no_cp.unique' => 'No CP sudah digunakan.',
            'quantity.integer' => 'Quantity harus berupa angka.',
            'qty_lot.integer' => 'Qty Lot harus berupa angka.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $cuttingPlan->update($validator->validated());

        return response()->json(['success' => true, 'message' => 'Data berhasil diperbarui.']);
    }

    public function destroy(CuttingPlan $cuttingPlan)
    {
        $cuttingPlan->delete();

        return response()->json(['success' => true, 'message' => 'Data berhasil dihapus.']);
    }

    public function users()
    {
        $users = \App\Models\User::select('id', 'name')->orderBy('name')->get();

        return response()->json($users);
    }

    public function materials(Request $request)
    {
        $search = $request->get('q', '');

        $query = Material::query();

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('kode_material', 'LIKE', "%{$search}%")
                    ->orWhere('deskripsi', 'LIKE', "%{$search}%")
                    ->orWhere('spesifikasi', 'LIKE', "%{$search}%");
            });
        }

        $materials = $query->orderBy('kode_material')
            ->limit(20)
            ->get(['kode_material', 'deskripsi', 'spesifikasi']);

        $results = $materials->map(function ($m) {
            return [
                'id' => $m->kode_material,
                'text' => $m->kode_material . ' - ' . $m->deskripsi,
            ];
        });

        return response()->json(['results' => $results]);
    }
}
