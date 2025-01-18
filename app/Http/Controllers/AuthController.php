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
    // Validasi input
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required|string',
    ]);

    // Cek apakah email terdaftar
    $user = User::where('email', $request->email)->first();
    if (!$user) {
        return back()->withErrors(['email' => 'Email tidak terdaftar.'])->withInput();
    }

    // Autentikasi user
    if (!Auth::attempt($credentials)) {
        return redirect()->back()->with('error', 'Email atau password salah.');
    }

    // Perbarui waktu login terakhir
    $user->update(['last_login' => now()]);

    // Akses berdasarkan semua role pengguna
    $roles = $user->roles->pluck('name')->toArray();

    // Redirect jika memiliki role superadmin
    if (in_array('superadmin', $roles)) {
        return redirect()->route('users.index')->with('login-sukses', 'Login berhasil sebagai SuperAdmin!');
    }

    // Jika pengguna memiliki kombinasi role, arahkan mereka ke halaman yang sesuai
    if (in_array('prepared', $roles) || in_array('Check1', $roles) || in_array('Check2', $roles)) {
        return redirect()->route('submissions.index')->with('login-sukses', 'Login berhasil sebagai Prepared, Check1, atau Check2!');
    }

    if (in_array('approved', $roles)) {
        return redirect()->route('submissions.index')->with('login-sukses', 'Login berhasil sebagai Approved!');
    }

    if (in_array('viewer', $roles)) {
        return redirect()->route('approval.history')->with('login-sukses', 'Login berhasil sebagai Viewer!');
    }

    // Default jika role tidak dikenali
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
