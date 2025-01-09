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
        // if (!$user) {
        //     return back()->with(['error' => 'Email tidak terdaftar.']);
        // }
        if (!$user) {
            return back()->withErrors(['email' => 'Email tidak terdaftar.'])->withInput();
        }

        // Cek apakah email terdaftar
        if (!Auth::attempt($credentials)) {
            return redirect()->back()->with('error', 'Email atau password salah.');
        }

        // Perbarui waktu login terakhir
        $user->update(['last_login' => now()]);
        // Redirect dengan pesan sukses
        return redirect()->route('users.index')->with('success', 'Login berhasil!');
    }


    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Logout berhasil!');
    }
}
