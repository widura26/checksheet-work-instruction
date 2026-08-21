<?php

namespace App\Http\Controllers;

use App\Models\Mdr;
use Illuminate\Http\Request;

class MdController extends Controller
{
    public function index()
    {
        return view('md.index');
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
}