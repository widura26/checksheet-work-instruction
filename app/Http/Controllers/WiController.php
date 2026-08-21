<?php

namespace App\Http\Controllers;

use App\Models\WiTrn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class WiController extends Controller
{
    public function index()
    {
        return view('wi.index');
    }

    public function data(Request $request)
    {
        if (! $request->ajax()) {
            abort(404);
        }

        $query = WiTrn::query()
            ->leftJoin('md_headers', 'wi_trns.md_header_id', '=', 'md_headers.id')
            ->leftJoin('cps', 'wi_trns.cp_id', '=', 'cps.id')
            ->leftJoinSub(
                DB::table('cp_details')
                    ->select('id_cp', DB::raw('MAX(shop_name) as shop_name'))
                    ->groupBy('id_cp'),
                'cpd',
                'cpd.id_cp',
                '=',
                'cps.no_cp'
            )
            ->select([
                'wi_trns.id',
                'wi_trns.part_no',
                'wi_trns.name',
                'wi_trns.mat_cp as mat',
                'md_headers.t1',
                'md_headers.h1',
                'md_headers.b1',
                'md_headers.l1',
                'md_headers.ws1',
                'md_headers.ws2',
                'md_headers.ws3',
                'md_headers.ws4',
                'md_headers.ws5',
                'cps.shop as shop1',
                'cpd.shop_name as shop2',
                'md_headers.des',
                'wi_trns.no_ctplan',
                'wi_trns.tgl_ctplan',
                'wi_trns.qty_lot',
                'wi_trns.no_bpm',
                'wi_trns.tgl_bpm',
                'wi_trns.tgl_trn',
                'wi_trns.keterangan',
            ]);

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('tgl_ctplan', function ($row) {
                return $row->tgl_ctplan
                    ? \Carbon\Carbon::parse($row->tgl_ctplan)->format('d-m-Y')
                    : '-';
            })
            ->editColumn('tgl_bpm', function ($row) {
                return $row->tgl_bpm
                    ? \Carbon\Carbon::parse($row->tgl_bpm)->format('d-m-Y')
                    : '-';
            })
            ->editColumn('tgl_trn', function ($row) {
                return $row->tgl_trn
                    ? \Carbon\Carbon::parse($row->tgl_trn)->format('d-m-Y')
                    : '-';
            })
            ->make(true);
    }
}
