<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
{
    // Validasi input: bisa berupa email atau ID-card
    $credentials = $request->validate([
        'loginIdentifier' => 'required|string',
        'password' => 'required|string',
    ]);

    // Cek apakah input berupa email atau ID-Card
 // Tentukan field berdasarkan input pengguna
 $fieldType = filter_var($request->loginIdentifier, FILTER_VALIDATE_EMAIL)
 ? 'email'
 : (is_numeric($request->loginIdentifier) && strlen($request->loginIdentifier) <= 8
     ? 'IDcard'
     : 'RFID');

    // Cari user berdasarkan email atau ID-card
    $user = User::where($fieldType, $request->loginIdentifier)->first();

    if (!$user) {
        return back()->withErrors(['loginIdentifier' => 'Email,ID-Card atau RFID tidak terdaftar.'])->withInput();
    }

    // Autentikasi pengguna
    if (!Auth::attempt([$fieldType => $request->loginIdentifier, 'password' => $request->password])) {
        return redirect()->back()->with('error', 'Email/ID-Card atau password salah.');
    }

    // Perbarui waktu login terakhir
    $user->update(['last_login' => now()]);

    // Akses berdasarkan semua role pengguna
    $roles = $user->roles->pluck('name')->toArray();

    // Redirect berdasarkan peran pengguna
    if (in_array('superadmin', $roles)) {
        return redirect()->route('users.index')->with('login-sukses', 'Login berhasil sebagai SuperAdmin!');
    }

    if (array_intersect(['prepared', 'Check1', 'Check2'], $roles)) {
        $userRoles = implode(', ', array_map('ucfirst', array_intersect(['prepared', 'Check1', 'Check2'], $roles)));
        return redirect()->route('submissions.index')->with('login-sukses', "Login berhasil sebagai {$userRoles}!");
    }


    if (in_array('approved', $roles)) {
        return redirect()->route('submissions.index')->with('login-sukses', 'Login berhasil sebagai Approved!');
    }

    if (in_array('viewer', $roles)) {
        return redirect()->route('approval.history')->with('login-sukses', 'Login berhasil sebagai Viewer!');
    }

    // Jika role tidak dikenali
    Auth::logout();
    return redirect()->route('login')->withErrors(['error' => 'Role Anda tidak dikenali. Silakan hubungi administrator.']);
}




    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Logout berhasil!');
    }
}
