<?php

namespace App\Http\Controllers;

use App\Models\Bagian;
use App\Models\Departemen;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class BagianController extends Controller
{
    public function index()
    {
        return view('pages.lokasi.bagian.index');
    }

    public function getData(Request $request)
    {
        if (!$request->ajax()) {
            abort(403);
        }
        $bagian = Bagian::with('departemen');

        return DataTables::of($bagian)
            ->addIndexColumn()
            ->addColumn('action', function ($bagian) {
                $buttons = '<div class="btn-group d-flex justify-content-center gap-2" role="group">';
                if (Auth::user()->hasRole(['admin'])) {
                    $buttons .= '
                    <form action="' . route('lokasi.bagian.toggle', $bagian->id) . '" method="POST">
                                        ' . csrf_field() . '
                                        <div class="form-check form-switch">
                                            <input type="checkbox" class="form-check-input" role="switch"
                                                onchange="this.form.submit()" id="flexSwitchCheckChecked" ' . ($bagian->is_active == true ? 'checked' : '') . '>
                                        </div>
                                    </form>
                ';
                }
                $buttons .= '<a href="' . route('lokasi.bagian.edit', $bagian->id) . '" class="btn btn-primary btn-sm">Edit</a>';
                $buttons .= '</div>';
                return $buttons;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function create()
    {
        if (!Auth::user()->hasRole(['admin'])) {
            abort(403);
        }
        $departemens = Departemen::all();

        return view('pages.lokasi.bagian.create', compact('departemens'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_bagian' => 'required|string|max:100',
            'departemen_id' => 'required|exists:departemens,id',
        ]);

        Bagian::create($request->all());

        return redirect()->route('lokasi.bagian.index')->with('success', 'Bagian created successfully.');
    }

    public function edit($id)
    {
        if (!Auth::user()->hasRole(['admin'])) {
            abort(403);
        }
        $bagian = Bagian::findOrFail($id);
        $departemens = Departemen::all();

        return view('pages.lokasi.bagian.edit', compact('bagian', 'departemens'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_bagian' => 'required|string|max:100',
            'departemen_id' => 'required|exists:departemens,id',
        ]);

        $bagian = Bagian::findOrFail($id);
        $bagian->update($request->all());

        return redirect()->route('lokasi.bagian.index')->with('success', 'Bagian updated successfully.');
    }

    public function destroy($id)
    {
        Bagian::findOrFail($id)->delete();

        return redirect()->route('lokasi.bagian.index')->with('success', 'Bagian deleted successfully.');
    }

    public function toggleBagian(Bagian $bagian)
    {
        $bagianActive = $bagian->is_active;
        $bagian->is_active = !$bagian->is_active;
        $bagian->save();
        Unit::where('bagian_id', $bagian->id)->update(['is_active' => !$bagianActive]);
        return redirect()->route('lokasi.bagian.index')->with('success', 'bagian Berhasil Di Non Aktifkan.');
    }
}
