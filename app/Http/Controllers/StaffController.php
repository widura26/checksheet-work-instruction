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
    public function index(Request $request)
    {
        $staffs = User::whereHas('roles', function ($query) {
            $query->whereIn('name', ['staff', 'viewer', 'admin']);
        })->with('roles')->get();

        return view('pages.staff.index', [
            'staffs' => $staffs,
        ]);
    }

    public function create()
    {
        $roles = Role::where('name', '<>', 'admin')->get();

        return view('pages.staff.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'nip' => 'required|unique:users|min:9',
            'password' => 'required|string|min:6',
            'role' => 'required|exists:roles,name',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'nip' => $request->nip,
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole($request->role);

        return redirect()->route('staff.index')->with('success', 'Staff berhasil dibuat.');
    }

    public function edit($id)
    {
        $staff = User::findOrFail($id);
        $roles = Role::all();

        return view('pages.staff.edit', compact('staff', 'roles'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email,' . $id,
            'nip' => 'required|unique:users,nip,' . $id,
            'password' => 'nullable|string|min:6',
            'role' => 'required|exists:roles,name',
        ]);

        $staff = User::findOrFail($id);

        $staff->name = $request->name;
        $staff->email = $request->email;
        $staff->nip = $request->nip;

        if ($request->filled('password')) {
            $staff->password = Hash::make($request->password);
        }

        $staff->save();

        $staff->syncRoles([$request->role]);

        return redirect()->route('staff.index')->with('success', 'Staff berhasil diperbarui.');
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
