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

        // Redirect berdasarkan role pengguna
        switch ($user->role->name) {
            case 'superadmin':
                return redirect()->route('users.index')->with('login-sukses', 'Login berhasil sebagai SuperAdmin!');

            case 'prepared':
                return redirect()->route('submissions.index')->with('login-sukses', 'Login berhasil sebagai Prepared!');

            case 'Check1':
                return redirect()->route('submissions.index')->with('login-sukses', 'Login berhasil sebagai Check1!');

            case 'Check2':
                return redirect()->route('submissions.index')->with('login-sukses', 'Login berhasil sebagai Check2!');

            case 'approvalManager':
                return redirect()->route('submissions.index')->with('login-sukses', 'Login berhasil sebagai Approved!');

            case 'viewer':
                return redirect()->route('approval.history')->with('login-sukses', 'Login berhasil sebagai Viewer !');

            default:
                // Jika role tidak dikenali
                Auth::logout(); // Logout pengguna
                return redirect()->route('login')->withErrors(['error' => 'Role Anda tidak dikenali. Silakan hubungi administrator.']);
        }
    }


    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Logout berhasil!');
    }
}
