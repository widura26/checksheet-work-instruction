<?php

namespace App\Http\Controllers;

use App\Models\CuttingPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CuttingPlanController extends Controller
{
    public function index()
    {
        return view('cutting-plan.index');
    }

    public function getData(Request $request)
    {
        if (! $request->ajax()) {
            abort(404);
        }

        $query = CuttingPlan::select([
            'id', 'id_cp', 'no_cp', 'mat', 'spec_mat',
            'qty', 'shop', 'grup', 'lot', 'qty_lot',
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
        $validator = Validator::make($request->all(), [
            'id_cp' => 'nullable|string|max:255',
            'no_cp' => 'required|string|max:255|unique:cutting_plans,no_cp',
            'mat' => 'nullable|string|max:255',
            'spec_mat' => 'nullable|string|max:255',
            'qty' => 'nullable|integer',
            'shop' => 'nullable|string|max:255',
            'grup' => 'nullable|string|max:255',
            'lot' => 'nullable|string|max:255',
            'qty_lot' => 'nullable|integer',
        ], [
            'no_cp.required' => 'No CP wajib diisi.',
            'no_cp.unique' => 'No CP sudah digunakan.',
            'qty.integer' => 'Qty harus berupa angka.',
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
        $validator = Validator::make($request->all(), [
            'id_cp' => 'nullable|string|max:255',
            'no_cp' => 'required|string|max:255|unique:cutting_plans,no_cp,' . $cuttingPlan->id . ',id',
            'mat' => 'nullable|string|max:255',
            'spec_mat' => 'nullable|string|max:255',
            'qty' => 'nullable|integer',
            'shop' => 'nullable|string|max:255',
            'grup' => 'nullable|string|max:255',
            'lot' => 'nullable|string|max:255',
            'qty_lot' => 'nullable|integer',
        ], [
            'no_cp.required' => 'No CP wajib diisi.',
            'no_cp.unique' => 'No CP sudah digunakan.',
            'qty.integer' => 'Qty harus berupa angka.',
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
}
