<?php

namespace App\Http\Controllers;

use App\Models\Cp;
use Illuminate\Http\Request;

class CpController extends Controller
{
    public function index()
    {
        return view('cp.index');
    }

    public function getData(Request $request)
    {
        if (! $request->ajax()) {
            abort(404);
        }

        $query = Cp::select([
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
}