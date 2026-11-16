<?php

namespace App\Http\Controllers;

use App\Models\Bagian;
use App\Models\Departemen;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DepartemenController extends Controller
{
    public function index()
    {
        return view('pages.lokasi.departemen.index');
    }

    public function data(Request $request)
    {
        if (! $request->ajax()) {
            abort(404);
        }

        $query = Departemen::query();

        if ($request->filled('search')) {
            $query->where('nama_departemen', 'like', '%' . $request->search . '%');
        }

        $perPage = (int) $request->get('per_page', 10);
        $departemens = $query->paginate($perPage);

        $departemens->setCollection($departemens->getCollection()->map(function ($departemen) {
            $buttons = '<div class="d-flex justify-content-center gap-2" role="group">';
            if (Auth::user()->hasRole(['admin'])) {
                $buttons .= '
                <form action="' . route('lokasi.departemen.toggle', $departemen->id) . '" method="POST">
                    ' . csrf_field() . '
                    <div class="form-check form-switch">
                        <input type="checkbox" class="form-check-input" role="switch"
                            onchange="this.form.submit()" ' . ($departemen->is_active == true ? 'checked' : '') . '>
                    </div>
                </form>
            ';
            }
            $buttons .= '<a href="' . route('lokasi.departemen.edit', $departemen->id) . '" class="btn btn-primary btn-sm">Edit</a>';

            return [
                'id' => $departemen->id,
                'nama_departemen' => $departemen->nama_departemen,
                'action' => $buttons,
            ];
        }));

        return response()->json($departemens);
    }

    public function create()
    {
        if (!Auth::user()->hasRole(['admin'])) {
            abort(403);
        }
        return view('pages.lokasi.departemen.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_departemen' => 'required|string|max:255',
        ]);

        Departemen::create($validated);

        return redirect()->route('lokasi.departemen.index')->with('success', 'Departemen created successfully.');
    }

    public function edit($id){
        if (!Auth::user()->hasRole(['admin'])) {
            abort(403);
        }
        $departemen = Departemen::findOrFail($id);

        return view('pages.lokasi.departemen.edit', compact('departemen'));
    }

    public function update(Request $request, $id){
        $validated = $request->validate([
            'nama_departemen' => 'required|string|max:255',
        ]);

        $departemen = Departemen::findOrFail($id);
        $departemen->update($validated);

        return redirect()->route('lokasi.departemen.index')->with('success', 'Departemen updated successfully.');
    }

    public function destroy($id)
    {
        try {
            Departemen::findOrFail($id)->delete();
            return redirect()->route('lokasi.departemen.index')->with('success', 'Departemen deleted successfully.');
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Gagal menghapus departemen: ' . $e->getMessage(), ['departemen_id' => $id]);
            return redirect()->route('lokasi.departemen.index')->with('error', 'Gagal menghapus departemen. Pastikan tidak ada data terkait.');
        }
    }

    public function toggleDepartemen(Departemen $departemen){
        try {
            DB::transaction(function () use ($departemen) {
                $departemenActive = (bool) $departemen->is_active;
                $newStatus = !$departemenActive;
                $departemen->is_active = $newStatus;
                $departemen->save();
                Bagian::where('departemen_id', $departemen->id)->update(['is_active' => $newStatus]);
                Unit::whereHas('bagian', function ($q) use ($departemen) {
                    $q->where('departemen_id', $departemen->id);
                })->update(['is_active' => $newStatus]);
            });

            $message = $departemen->is_active ? 'Departemen berhasil diaktifkan.' : 'Departemen berhasil dinonaktifkan.';
            return redirect()->route('lokasi.departemen.index')->with('success', $message);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Gagal mengubah status departemen: ' . $e->getMessage(), ['departemen_id' => $departemen->id]);
            return redirect()->route('lokasi.departemen.index')->with('error', 'Gagal mengubah status departemen.');
        }
    }
}
