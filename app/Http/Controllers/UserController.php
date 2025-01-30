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
        $users = User::with('roles', 'kategoris')
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
            'role' => 'required|in:prepared,Check1,Check2,approved,viewer',
            'kategori_id' =>'exists:tbl_kategori,id',
            'departement' => 'in:HRGA,FAS,PPIC',
        ]);

        // Generate password based on ID Card securely
        $generatedPassword = Hash::make($request->input('ID-card'));

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'IDcard' => $request->input('ID-card'),
            'RFID' => $request->rfid,
            'id_departement' => $this->getDepartementID($request->departement),
            'password' => $generatedPassword,
        ]);

        // Attach role to user
        $roleId = Role::where('name', $request->role)->value('id');
        if ($roleId) {
            $user->roles()->attach($roleId);
        }

        if ($request->filled('kategori_id')) {
            $kategoriIds = Kategori::whereIn('id', (array) $request->kategori_id)->pluck('id')->toArray();
            $user->kategoris()->attach($kategoriIds);
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

        // Ambil semua kategori yang tersedia
        $categories = Kategori::all();

        // Ambil kategori yang sudah dimiliki oleh user
        $userCategories = $user->kategoris->pluck('id')->toArray();

        return response()->json([
            'user' => $user,
            'roles' => $roles,
            'categories' => $categories,
            'userRoles' => $userRoles,
            'userCategories' => $userCategories, // Kirim kategori yang dimiliki oleh user
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
        $user = User::findOrFail($userId);

        // Validasi data
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:tbl_users,email,' . $userId,
            'ID-card' => 'nullable|string|max:50|unique:tbl_users,IDcard,' . $userId,
            'password' => 'nullable|string|min:8',
            'role' => 'nullable|in:prepared,Check1,Check2,approved,viewer',
            'kategori_id' => 'nullable|exists:tbl_kategori,id',
        ]);

        // Mencegah perubahan nama dan email
        if ($request->name !== $user->name || $request->email !== $user->email) {
            return redirect()->route('users.index')->with('error', 'Nama dan Email tidak dapat diubah.');
        }

        // Cek jika ID-card ingin diubah dan sudah ada di pengguna lain
        if ($request->input('ID-card') !== $user->IDcard) {
            $existingUser = User::where('IDcard', $request->input('ID-card'))->first();
            if ($existingUser) {
                return redirect()->route('users.index')
                    ->with('error', "ID-card sudah digunakan oleh pengguna lain");
            }
        }

        // Update data yang diperbolehkan
        $user->IDcard = $request->input('ID-card');

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        // Update role jika dipilih
        if ($request->role) {
            $roleId = Role::where('name', $request->role)->value('id');
            if ($roleId && !$user->roles->pluck('id')->contains($roleId)) {
                $user->roles()->attach($roleId);
                return redirect()->route('users.index')->with('success', 'User berhasil diperbarui dan role ditambahkan.');
            } else {
                return redirect()->route('users.index')->with('error', 'Role sudah dimiliki oleh pengguna.');
            }
        }

        // Tambahkan kategori baru jika dipilih
        if ($request->filled('kategori_id')) {
            $kategoriId = $request->kategori_id;
            if (!$user->kategoris->pluck('id')->contains($kategoriId)) {
                $user->kategoris()->attach($kategoriId);
                return redirect()->route('users.index')->with('success', 'Kategori baru berhasil ditambahkan.');
            } else {
                return redirect()->route('users.index')->with('error', 'Kategori sudah dimiliki oleh pengguna.');
            }
        }

        return redirect()->route('users.index')->with('success', 'User berhasil diperbarui.');
    }

    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);

        // Toggle status
        $user->status = !$user->status;
        $user->save();

        return redirect()->back()->with('success', 'Status pengguna berhasil diperbarui.');
    }
    public function getRolesAndCategories(User $user)
    {
        return response()->json([
            'roles' => $user->roles->map(function ($role) {
                return ['id' => $role->id, 'name' => $role->name];
            }),
            'kategories' => $user->kategoris->map(function ($kategori) {
                return ['id' => $kategori->id, 'nama_kategori' => $kategori->nama_kategori];
            }),
        ]);
    }

    public function deleteRoleOrKategori(Request $request, User $user)
    {
        $roleId = $request->role_id;
        $kategoriId = $request->kategori_id;

        if ($roleId) {
            $user->roles()->detach($roleId);
            return redirect()->route('users.index')->with('success', 'Role berhasil dihapus.');
        }

        if ($kategoriId) {
            $user->kategoris()->detach($kategoriId);
            return redirect()->route('users.index')->with('success', 'Kategori berhasil dihapus.');
        }

        return redirect()->route('users.index')->with('error', 'Tidak ada perubahan.');
    }


}
