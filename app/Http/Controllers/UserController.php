<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
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
        return view('Pages.Users.create', compact('roles', 'users'));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'name' => 'nullable|string|max:100',
            'ID-card' => 'nullable|string|max:8',
            'email' => 'nullable|string|email|max:100',
            'password' => 'nullable|string|min:8',
            'role' => 'required|in:prepared,Check1,Check2,approved,viewer',
            'departement' => 'nullable|in:HRGA,FAS,PPIC',
        ]);

        // Cari user berdasarkan email atau ID-card
        $userQuery = User::query();
        if ($request->filled('email')) {
            $userQuery->where('email', $request->email);
        }
        if ($request->filled('ID-card')) {
            $userQuery->orWhere('ID-card', $request->input('ID-card'));
        }
        $user = $userQuery->first();

        if ($user) {
            // Jika user sudah ada, tambahkan role
            $roleId = Role::where('name', $request->role)->value('id');
            if ($roleId && !$user->roles->contains('id', $roleId)) {
                $user->roles()->attach($roleId);
            }
        } else {
            // Jika user belum ada, buat user baru
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'ID-card' => $request->input('ID-card'),
                'id_departement' => $this->getDepartementID($request->departement),
                'password' => $request->password ? Hash::make($request->password) : null,
            ]);

            // Tambahkan role ke user baru
            $roleId = Role::where('name', $request->role)->value('id');
            if ($roleId) {
                $user->roles()->attach($roleId);
            }
        }

        return redirect()->route('users.index')->with('success', 'User berhasil disimpan.');
    }


    /**
     * Get role ID from role name.
     */
    private function getDepartementID($departement_name)
    {
        return \App\Models\Departement::where('nama_departement', $departement_name)->value('id');
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
            'userRoles' => $userRoles, // Tambahkan role yang dimiliki user
        ]);
    }

    public function update(Request $request, $userId)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:tbl_users,email,' . $userId,
            'ID-card' => 'nullable|string|max:50|unique:tbl_users,ID-card,' . $userId,
            'password' => 'nullable|string|min:8',
            'roles' => 'required|array', // Validasi roles sebagai array
            'roles.*' => 'exists:roles,name', // Validasi setiap role harus ada di database
        ]);

        $user = User::findOrFail($userId);

        // Update user fields
        $user->name = $request->name;
        $user->email = $request->email;
        $user->{'ID-card'} = $request->{'ID-card'};

        if ($request->password) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        // Update user roles
        $roleIds = Role::whereIn('name', $request->roles)->pluck('id')->toArray(); // Ambil ID roles dari nama
        $user->roles()->sync($roleIds); // Sinkronkan role dengan user

        return redirect()->route('users.index')->with('success', 'User berhasil diperbarui.');
    }


    public function checkEmail(Request $request)
    {
        $request->validate([
            'email' => 'sometimes|email',
            'ID-card' => 'sometimes|',
        ]);

        $user = User::with('roles')->where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'exists' => false,
                'roles' => [],
            ]);
        }

        return response()->json([
            'exists' => true,
            'roles' => $user->roles
                ->where('name', '!=', 'superadmin') // Kecualikan superadmin
                ->pluck('name')
                ->toArray(),
        ]);
    }



}
