<?php

namespace App\Http\Controllers;

use App\Models\SttppFab;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class SttpController extends Controller
{
    public function index()
    {
        return view('sttp.index');
    }

    public function data(Request $request)
    {
        if (! $request->ajax()) {
            abort(404);
        }

        $query = SttppFab::query()
            ->leftJoin('sttpps', 'sttpp_fabs.sttpp_id', '=', 'sttpps.id')
            ->leftJoin('md_headers', 'sttpp_fabs.md_header_id', '=', 'md_headers.id')
            ->leftJoin('okas', 'sttpp_fabs.oka_id', '=', 'okas.id')
            ->leftJoin('projects', 'sttpp_fabs.project_id', '=', 'projects.id')
            ->leftJoin('users as pengirim', 'sttpp_fabs.pengirim_id', '=', 'pengirim.id')
            ->leftJoin('users as penerima', 'sttpp_fabs.penerima_id', '=', 'penerima.id')
            ->select([
                'sttpp_fabs.id',
                'sttpps.nosttpp',
                'md_headers.part_no as no_part',
                'sttpp_fabs.name_part',
                'sttpp_fabs.rev',
                'okas.oka as oka',
                'projects.project_code as proyek',
                'sttpp_fabs.lot',
                'sttpp_fabs.qty_lot',
                'sttpp_fabs.qty_kirim',
                'sttpp_fabs.tgl_kirim',
                'sttpp_fabs.dari',
                'sttpp_fabs.ke',
                'pengirim.name as pengirim',
                'penerima.name as penerima',
                'sttpp_fabs.keterangan',
            ]);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('sttpps.nosttpp', 'LIKE', "%{$search}%")
                    ->orWhere('md_headers.part_no', 'LIKE', "%{$search}%")
                    ->orWhere('sttpp_fabs.name_part', 'LIKE', "%{$search}%")
                    ->orWhere('projects.project_code', 'LIKE', "%{$search}%")
                    ->orWhere('okas.oka', 'LIKE', "%{$search}%");
            });
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('tgl_kirim', function ($row) {
                return $row->tgl_kirim
                    ? \Carbon\Carbon::parse($row->tgl_kirim)->format('d-m-Y')
                    : '-';
            })
            ->make(true);
    }
}
