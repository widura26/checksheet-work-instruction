<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class StaffController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin');
    }
    public function index()
    {
        $roles = Role::all();

        return view('pages.staff.index', compact('roles'));
    }

    public function data(Request $request)
    {
        if (! $request->ajax()) {
            abort(404);
        }

        $query = User::whereHas('roles', function ($query) {
            $query->whereIn('name', ['staff', 'viewer', 'admin']);
        })->with('roles');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('nip', 'like', "%{$search}%");
            });
        }

        $perPage = (int) $request->get('per_page', 10);
        $staffs = $query->paginate($perPage);

        $staffs->setCollection($staffs->getCollection()->map(function ($staff) {
            if (auth()->user()->hasRole(['admin'])) {
                $status = '<form action="' . route('staff.status', $staff->nip) . '" method="POST" class="d-inline">
                        ' . csrf_field() . '
                        <div class="form-check form-switch">
                            <input type="checkbox" class="form-check-input" role="switch" onchange="this.form.submit()" ' . ($staff->is_active ? 'checked' : '') . '>
                        </div>
                    </form>';
            } else {
                $status = $staff->is_active
                    ? '<span class="badge bg-success">Aktif</span>'
                    : '<span class="badge bg-secondary">Nonaktif</span>';
            }

                $action = '<button type="button" class="btn btn-sm btn-warning me-1" onclick="openEditModal(' . $staff->id . ')">Edit</button>';
            $action .= '<form action="' . route('staff.destroy', $staff->id) . '" method="POST" class="d-inline" onsubmit="return confirm(\'Apakah Anda yakin ingin menghapus user ini?\')">
                    ' . csrf_field() . '
                    ' . method_field('DELETE') . '
                    <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                </form>';

            return [
                'id' => $staff->id,
                'name' => $staff->name,
                'email' => $staff->email,
                'nip' => $staff->nip,
                'role' => $staff->roles->isNotEmpty()
                    ? $staff->roles->pluck('name')->map(fn($r) => ucfirst($r))->join(', ')
                    : '-',
                'status' => $status,
                'action' => $action,
            ];
        }));

        return response()->json($staffs);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'nip' => 'required|unique:users|min:9',
            'password' => 'required|string|min:6',
            'role' => 'required|array',
            'role.*' => 'exists:roles,name',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'nip' => $request->nip,
            'password' => Hash::make($request->password),
        ]);

        $user->syncRoles($request->role);

        return redirect()->route('staff.index')->with('success', 'Staff berhasil dibuat.');
    }

    public function edit(Request $request, $id)
    {
        $staff = User::findOrFail($id);
        $roles = Role::all();
        $assigned = $staff->roles->pluck('name')->toArray();

        if ($this->isApiRequest($request)) {
            return response()->json([
                'staff' => $staff,
                'roles' => $roles,
                'assigned' => $assigned,
            ]);
        }

        return view('pages.staff.edit', compact('staff', 'roles'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email,' . $id,
            'nip' => 'required|unique:users,nip,' . $id,
            'password' => 'nullable|string|min:6',
            'role' => 'required|array',
            'role.*' => 'exists:roles,name',
        ]);

        $staff = User::findOrFail($id);

        $staff->name = $request->name;
        $staff->email = $request->email;
        $staff->nip = $request->nip;

        if ($request->filled('password')) {
            $staff->password = Hash::make($request->password);
        }

        $staff->save();

        $staff->syncRoles($request->role);

        if ($this->isApiRequest($request)) {
            return response()->json(['success' => 'Staff berhasil diperbarui.']);
        }

        return redirect()->route('staff.index')->with('success', 'Staff berhasil diperbarui.');
    }

    protected function isApiRequest(Request $request)
    {
        return $request->ajax() || $request->wantsJson();
    }
    public function changeStatus($nip)
    {
        try {
            $staff = User::where('nip', $nip)->firstOrFail();
            $staff->is_active = !$staff->is_active;
            $staff->save();
            return redirect()->route('staff.index')->with('success', 'Status Staff berhasil diperbarui.');
        } catch (\Throwable $th) {
            return redirect()->route('staff.index')->with('error', 'Staff tidak ditemukan.');
        }
    }
    public function destroy(Request $request, $id)
    {
        $staff = User::findOrFail($id);
        $staff->delete();

        if ($request->ajax()) {
            return response()->json(['success' => 'Staff berhasil dihapus.']);
        }

        return redirect()->route('staff.index')->with('success', 'Staff berhasil dihapus.');
    }
}
