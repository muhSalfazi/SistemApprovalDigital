<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Filter users tanpa role 'superadmin'
        $users = User::with('role')
            ->whereHas('role', function ($query) {
                $query->where('name', '!=', 'superadmin');
            })
            ->get();

        return view('Pages.Users.index', compact('users'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('Pages.users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'name' => 'nullable|string|max:20|unique:tbl_users,name',
            'email' => 'nullable|string|max:20|unique:tbl_users,email',
            'password' => 'nullable|string|min:8',
            'role' => 'required|in:admin,prepared,approver,viewer',
        ]);

        // Simpan user ke database
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email ?? null;
        $user->password = $request->password ? Hash::make($request->password) : null;
        $user->role_id = $this->getRoleId($request->role); // Get role ID
        $user->save();

        return redirect()->route('users.index')->with('success', 'User berhasil dibuat.');
    }

    /**
     * Get role ID from role name.
     */
    private function getRoleId($roleName)
    {
        return \App\Models\Role::where('name', $roleName)->value('id');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User berhasil dihapus.');
    }

    public function edit(User $user)
    {
        $roles = \App\Models\Role::where('name', '!=', 'superadmin')->get();
        return response()->json([
            'user' => $user,
            'roles' => $roles,
        ]);
    }

    public function update(Request $request, $userId)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:tbl_users,email,' . $userId,
        'password' => 'nullable|string|min:8',
        'role' => 'required|exists:tbl_roles,id', // Pastikan role adalah ID yang valid di tabel roles
    ]);

    $user = User::findOrFail($userId);
    $user->name = $request->name;
    $user->email = $request->email;

    if ($request->password) {
        $user->password = Hash::make($request->password);
    }

    $user->role_id = $request->role; // Langsung gunakan role sebagai role_id
    $user->save();

    return redirect()->route('users.index')->with('success', 'User berhasil diperbarui.');
}




}
