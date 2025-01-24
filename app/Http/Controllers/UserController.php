<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
     /**
     * Get role ID from role name.
     */
    private function getDepartementID($departement_name)
    {
        return \App\Models\Departement::where('nama_departement', $departement_name)->value('id');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Ambil semua role kecuali superadmin
        $roles = Role::where('name', '!=', 'superadmin')->get();

        // Ambil semua user yang tidak memiliki role superadmin
        $users = User::with('roles')
            ->whereDoesntHave('roles', function ($query) {
                $query->where('name', 'superadmin');
            })
            ->get();

        return view('Pages.Users.index', compact('users', 'roles'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
{
    $roles = Role::where('name', '!=', 'superadmin')->get();
    $users = User::with('roles')->get(); // Ambil semua user beserta role mereka
    $categories = Kategori::all(); // Ambil semua kategori dari tabel kategori

    return view('Pages.Users.create', compact('roles', 'users', 'categories'));
}

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'ID-card' => 'required|string|max:8|unique:tbl_users,IDcard',
            'email' => 'required|string|email|max:100|unique:tbl_users,email',
            'rfid' => 'required|string|max:20|unique:tbl_users,rfid',
            'password' => 'required|string|min:8',
            'role' => 'required|in:prepared,Check1,Check2,approved,viewer',
            'kategori_id' => [
                'nullable',
                function ($attribute, $value, $fail) use ($request) {
                    if (in_array($request->role, ['approved', 'viewer']) && $value !== null) {
                        $fail('Kategori tidak diperlukan untuk peran ini.');
                    }
                },
                'exists:tbl_kategori,id'
            ],
            'departement' => [
                'nullable',
                function ($attribute, $value, $fail) use ($request) {
                    if (in_array($request->role, ['approved', 'viewer']) && $value !== null) {
                        $fail('Departemen tidak diperlukan untuk peran ini.');
                    }
                },
                'in:HRGA,FAS,PPIC'
            ],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'IDcard' => $request->input('ID-card'),
            'RFID' => $request->rfid,
            'id_kategori' => $request->role == 'approved' || $request->role == 'viewer' ? null : $request->kategori_id,
            'id_departement' => $request->role == 'approved' || $request->role == 'viewer' ? null : $this->getDepartementID($request->departement),
            'password' => Hash::make($request->password),
        ]);

        // Attach role to user
        $roleId = Role::where('name', $request->role)->value('id');
        if ($roleId) {
            $user->roles()->attach($roleId);
        }

        return redirect()->route('users.index')->with('success', 'User baru berhasil dibuat.');
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
        $roles = Role::where('name', '!=', 'superadmin')->get(); // Ambil semua role kecuali superadmin
        $userRoles = $user->roles->pluck('name')->toArray(); // Ambil role yang dimiliki user

        return response()->json([
            'user' => $user,
            'roles' => $roles,
            'userRoles' => $userRoles,
        ]);
    }

    public function editID($userId)
    {
        $user = User::with('roles')->findOrFail($userId);

        // Gunakan nama tabel yang benar jika berbeda
        $roles = Role::where('name', '!=', 'superadmin')->get();

        return response()->json([
            'user' => $user,
            'roles' => $roles,
            'userRoles' => $user->roles->pluck('name')->toArray(),
        ]);

    }


    public function update(Request $request, $userId)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:tbl_users,email,' . $userId,
            'ID-card' => 'nullable|string|max:50|unique:tbl_users,IDcard,' . $userId,
            'password' => 'nullable|string|min:8',
            'role' => 'nullable|in:prepared,Check1,Check2,approved,viewer',
        ]);

        $user = User::findOrFail($userId);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->IDcard = $request->input('ID-card');

        if ($request->password) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        // Cek apakah role sudah ada pada user sebelum menambahkannya
        if ($request->role) {
            $roleId = Role::where('name', $request->role)->value('id');

            if ($roleId && !$user->roles->pluck('id')->contains($roleId)) {
                $user->roles()->attach($roleId);
                return redirect()->route('users.index')->with('success', 'User berhasil diperbarui dan role ditambahkan.');
            } else {
                return redirect()->route('users.index')->with('error', 'Role sudah dimiliki oleh pengguna.');
            }
        }

        return redirect()->route('users.index')->with('success', 'User berhasil diperbarui.');
    }


}
