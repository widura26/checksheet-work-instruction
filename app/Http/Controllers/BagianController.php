<?php

namespace App\Http\Controllers;

use App\Models\Bagian;
use App\Models\Departemen;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
        $validated = $request->validate([
            'nama_bagian' => 'required|string|max:100',
            'departemen_id' => 'required|exists:departemens,id',
        ]);

        Bagian::create($validated);

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
        $validated = $request->validate([
            'nama_bagian' => 'required|string|max:100',
            'departemen_id' => 'required|exists:departemens,id',
        ]);

        $bagian = Bagian::findOrFail($id);
        $bagian->update($validated);

        return redirect()->route('lokasi.bagian.index')->with('success', 'Bagian updated successfully.');
    }

    public function destroy($id)
    {
        try {
            Bagian::findOrFail($id)->delete();
            return redirect()->route('lokasi.bagian.index')->with('success', 'Bagian deleted successfully.');
        } catch (\Throwable $e) {
            Log::error('Gagal menghapus bagian: ' . $e->getMessage(), ['bagian_id' => $id]);
            return redirect()->route('lokasi.bagian.index')->with('error', 'Gagal menghapus bagian. Pastikan tidak ada data terkait.');
        }
    }

    public function toggleBagian(Bagian $bagian)
    {
        try {
            DB::transaction(function () use ($bagian) {
                $bagianActive = (bool) $bagian->is_active;
                $newStatus = !$bagianActive;
                $bagian->is_active = $newStatus;
                $bagian->save();
                Unit::where('bagian_id', $bagian->id)->update(['is_active' => $newStatus]);
            });

            $message = $bagian->is_active ? 'Bagian berhasil diaktifkan.' : 'Bagian berhasil dinonaktifkan.';
            return redirect()->route('lokasi.bagian.index')->with('success', $message);
        } catch (\Throwable $e) {
            Log::error('Gagal mengubah status bagian: ' . $e->getMessage(), ['bagian_id' => $bagian->id]);
            return redirect()->route('lokasi.bagian.index')->with('error', 'Gagal mengubah status bagian.');
        }
    }
}
