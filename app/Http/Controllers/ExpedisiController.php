<?php

namespace App\Http\Controllers;

use App\Models\Exp;
use Illuminate\Http\Request;

class ExpedisiController extends Controller
{
    public function index()
    {
        return view('ekspedisi.index');
    }

    public function data(Request $request)
    {
        if (! $request->ajax()) {
            abort(404);
        }

        $query = Exp::select([
            'id', 'no_exp', 'lot', 'tgl_kirim', 'tgl_kembali',
            'tujuan', 'ket_exp',
        ]);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('no_exp', 'LIKE', "%{$search}%")
                    ->orWhere('lot', 'LIKE', "%{$search}%")
                    ->orWhere('tujuan', 'LIKE', "%{$search}%")
                    ->orWhere('ket_exp', 'LIKE', "%{$search}%");
            });
        }

        $perPage = (int) $request->get('per_page', 10);
        $exps = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json($exps);
    }
}
