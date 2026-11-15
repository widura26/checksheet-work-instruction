<?php

namespace App\Http\Controllers;

use App\Models\Bagian;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class UnitController extends Controller
{
    public function index()
    {
        return view('pages.lokasi.unit.index');
    }

    public function data()
    {
        $unit = Unit::with('bagian');

        return DataTables::of($unit)
            ->addIndexColumn()
            ->addColumn('action', function ($unit) {
                $buttons = '<div class="btn-group d-flex justify-content-center gap-2" role="group">';
                if (Auth::user()->hasRole(['admin'])) {
                    $buttons .= '
                    <form action="' . route('lokasi.unit.toggle', $unit->id) . '" method="POST">
                                        ' . csrf_field() . '
                                        <div class="form-check form-switch">
                                            <input type="checkbox" class="form-check-input" role="switch"
                                                onchange="this.form.submit()" id="flexSwitchCheckChecked" ' . ($unit->is_active == true ? 'checked' : '') . '>
                                        </div>
                                    </form>
                ';
                }
                $buttons .= '<a href="' . route('lokasi.unit.edit', $unit->id) . '" class="btn btn-primary btn-sm">Edit</a>';
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
        $units = unit::all();
        $bagians = Bagian::all();
        return view('pages.lokasi.unit.create', compact('units', 'bagians'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_unit' => 'required|string|max:255',
            'bagian_id' => 'required|exists:bagians,id',
        ]);

        Unit::create($request->all());

        return redirect()->route('lokasi.unit.index')->with('success', 'Unit created successfully.');
    }

    public function edit($id)
    {
        if (!Auth::user()->hasRole(['admin'])) {
            abort(403);
        }
        $unit = Unit::findOrFail($id);
        $units = unit::all();
        $bagians = Bagian::all();
        return view('pages.lokasi.unit.edit', compact('unit', 'units', 'bagians'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_unit' => 'required|string|max:255',
            'bagian_id' => 'required|exists:bagians,id',
        ]);

        $unit = Unit::findOrFail($id);
        $unit->update($request->all());

        return redirect()->route('lokasi.unit.index')->with('success', 'Unit updated successfully.');
    }

    public function destroy($id)
    {
        Unit::findOrFail($id)->delete();

        return redirect()->route('lokasi.unit.index')->with('success', 'Unit deleted successfully.');
    }

    public function toggleUnit(Unit $unit)
    {
        $unit->is_active = !$unit->is_active;
        $unit->save();
        return redirect()->route('lokasi.unit.index')->with('success', 'unit Berhasil Di Non Aktifkan.');
    }
}
