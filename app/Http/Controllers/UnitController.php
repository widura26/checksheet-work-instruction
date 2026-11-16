<?php

namespace App\Http\Controllers;

use App\Models\Bagian;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
        $validated = $request->validate([
            'nama_unit' => 'required|string|max:255',
            'bagian_id' => 'required|exists:bagians,id',
        ]);

        Unit::create($validated);

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
        $validated = $request->validate([
            'nama_unit' => 'required|string|max:255',
            'bagian_id' => 'required|exists:bagians,id',
        ]);

        $unit = Unit::findOrFail($id);
        $unit->update($validated);

        return redirect()->route('lokasi.unit.index')->with('success', 'Unit updated successfully.');
    }

    public function destroy($id)
    {
        try {
            Unit::findOrFail($id)->delete();
            return redirect()->route('lokasi.unit.index')->with('success', 'Unit deleted successfully.');
        } catch (\Throwable $e) {
            Log::error('Gagal menghapus unit: ' . $e->getMessage(), ['unit_id' => $id]);
            return redirect()->route('lokasi.unit.index')->with('error', 'Gagal menghapus unit. Pastikan tidak ada data terkait.');
        }
    }

    public function toggleUnit(Unit $unit)
    {
        try {
            $unit->is_active = !$unit->is_active;
            $unit->save();
            $message = $unit->is_active ? 'Unit berhasil diaktifkan.' : 'Unit berhasil dinonaktifkan.';
            return redirect()->route('lokasi.unit.index')->with('success', $message);
        } catch (\Throwable $e) {
            Log::error('Gagal mengubah status unit: ' . $e->getMessage(), ['unit_id' => $unit->id]);
            return redirect()->route('lokasi.unit.index')->with('error', 'Gagal mengubah status unit.');
        }
    }
}
