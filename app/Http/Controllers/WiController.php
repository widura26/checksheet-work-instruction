<?php

namespace App\Http\Controllers;

use App\Models\MdHeader;
use App\Models\WiTrn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class WiController extends Controller
{
    public function index()
    {
        $mdHeaders = MdHeader::orderBy('part_no')->get(['id', 'part_no', 'title']);

        return view('wi.index', compact('mdHeaders'));
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
                'wi_trns.md_header_id',
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
            ->make(true);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'part_no' => 'nullable|string|max:255',
            'name' => 'nullable|string|max:255',
            'mat_cp' => 'nullable|string|max:255',
            'md_header_id' => 'nullable|exists:md_headers,id',
            'no_ctplan' => 'nullable|string|max:255',
            'tgl_ctplan' => 'nullable|date',
            'qty_lot' => 'nullable|integer',
            'no_bpm' => 'nullable|string|max:255',
            'tgl_bpm' => 'nullable|date',
            'tgl_trn' => 'nullable|date',
            'keterangan' => 'nullable|string',
        ], [
            'md_header_id.exists' => 'Manufacture Drawing tidak ditemukan.',
            'tgl_ctplan.date' => 'Tgl Cp harus berupa tanggal.',
            'tgl_bpm.date' => 'Tgl Bpm harus berupa tanggal.',
            'tgl_trn.date' => 'Tgl WI Trn harus berupa tanggal.',
            'qty_lot.integer' => 'Qty/lot harus berupa angka.',
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
            'part_no' => 'nullable|string|max:255',
            'name' => 'nullable|string|max:255',
            'mat_cp' => 'nullable|string|max:255',
            'md_header_id' => 'nullable|exists:md_headers,id',
            'no_ctplan' => 'nullable|string|max:255',
            'tgl_ctplan' => 'nullable|date',
            'qty_lot' => 'nullable|integer',
            'no_bpm' => 'nullable|string|max:255',
            'tgl_bpm' => 'nullable|date',
            'tgl_trn' => 'nullable|date',
            'keterangan' => 'nullable|string',
        ], [
            'md_header_id.exists' => 'Manufacture Drawing tidak ditemukan.',
            'tgl_ctplan.date' => 'Tgl Cp harus berupa tanggal.',
            'tgl_bpm.date' => 'Tgl Bpm harus berupa tanggal.',
            'tgl_trn.date' => 'Tgl WI Trn harus berupa tanggal.',
            'qty_lot.integer' => 'Qty/lot harus berupa angka.',
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
