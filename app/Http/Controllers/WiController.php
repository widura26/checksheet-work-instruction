<?php

namespace App\Http\Controllers;

use App\Models\CuttingPlan;
use App\Models\WiTrn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WiController extends Controller
{
    public function index()
    {
        $cuttingPlans = CuttingPlan::orderBy('no_cp')->get(['id', 'no_cp']);

        return view('wi.index', compact('cuttingPlans'));
    }

    public function data(Request $request)
    {
        if (! $request->ajax()) {
            abort(404);
        }

        $perPage = (int) $request->get('per_page', 10);
        $wiTrns = WiTrn::query()
            ->leftJoin('cutting_plans', 'work_instructions.cutting_plan_id', '=', 'cutting_plans.id')
            ->select([
                'work_instructions.id',
                'work_instructions.cutting_plan_id',
                'work_instructions.ket_wi',
                'work_instructions.rev',
                'work_instructions.estimasi',
                'work_instructions.note',
                'cutting_plans.no_cp as no_cp',
            ])
            ->orderBy('work_instructions.created_at', 'desc')
            ->paginate($perPage);

        return response()->json($wiTrns);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cutting_plan_id' => 'nullable|exists:cutting_plans,id',
            'keterangan' => 'nullable|string',
            'rev' => 'nullable|integer',
            'estimasi' => 'nullable|date',
            'note' => 'nullable|string',
        ], [
            'cutting_plan_id.exists' => 'Cutting Plan tidak ditemukan.',
            'rev.integer' => 'Revisi harus berupa angka.',
            'estimasi.date' => 'Estimasi harus berupa tanggal.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        WiTrn::create($validator->validated());

        return response()->json(['success' => true, 'message' => 'Data berhasil ditambahkan.']);
    }

    public function update(Request $request, WiTrn $wiTrn)
    {
        $validator = Validator::make($request->all(), [
            'cutting_plan_id' => 'nullable|exists:cutting_plans,id',
            'keterangan' => 'nullable|string',
            'rev' => 'nullable|integer',
            'estimasi' => 'nullable|date',
            'note' => 'nullable|string',
        ], [
            'cutting_plan_id.exists' => 'Cutting Plan tidak ditemukan.',
            'rev.integer' => 'Revisi harus berupa angka.',
            'estimasi.date' => 'Estimasi harus berupa tanggal.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $wiTrn->update($validator->validated());

        return response()->json(['success' => true, 'message' => 'Data berhasil diperbarui.']);
    }

    public function destroy(WiTrn $wiTrn)
    {
        $wiTrn->delete();

        return response()->json(['success' => true, 'message' => 'Data berhasil dihapus.']);
    }
}
