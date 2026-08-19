<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Proses;

class ProsesController extends Controller
{
    public function index()
    {
        return view('master.proses.index');
    }

    public function data(Request $request)
    {
        if (! $request->ajax()) {
            abort(404);
        }

        $query = Proses::select(['id', 'id_proses', 'kode_proses', 'nama', 'id_kel']);

        if ($request->filled('kode_proses')) {
            $query->where('kode_proses', 'LIKE', '%' . $request->kode_proses . '%');
        }

        if ($request->filled('nama')) {
            $query->where('nama', 'LIKE', '%' . $request->nama . '%');
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->make(true);
    }
}