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
    public function showLoginRFID()
    {
        return view('auth.login-rfid');
    }

    public function login(Request $request)
    {
        // Validasi input: bisa berupa email atau ID-card
        $credentials = $request->validate([
            'loginIdentifier' => 'required|string',
            'password' => 'required|string',
        ]);

        // Tentukan field berdasarkan input pengguna (hanya email atau ID-card)
        if (filter_var($request->loginIdentifier, FILTER_VALIDATE_EMAIL)) {
            $fieldType = 'email';
        } elseif (is_numeric($request->loginIdentifier) && strlen($request->loginIdentifier) <= 8) {
            $fieldType = 'IDcard';
        } else {
            return back()->withErrors(['loginIdentifier' => 'Gunakan email atau ID Card yang valid.'])->withInput();
        }

        // Cari user berdasarkan email atau ID-card
        $user = User::where($fieldType, $request->loginIdentifier)->first();

        if (!$user) {
            return back()->withErrors(['loginIdentifier' => 'Email atau ID-Card tidak terdaftar.'])->withInput();
        }

        // Jika akun tidak aktif dan bukan superadmin, tampilkan error
        if ($user->status == 0 && !$user->roles->pluck('name')->contains('superadmin')) {
            return redirect()->back()->with('error', 'Akun Anda telah dinon-aktifkan. Hubungi admin untuk mengaktifkan kembali');
        }


        // Periksa jumlah percobaan gagal login dari session
        $attempts = session()->get('login_attempts_' . $user->id, 0);

        if ($attempts >= 5) {
            // Nonaktifkan akun jika sudah mencapai batas
            $user->update(['status' => 0]);
            session()->forget('login_attempts_' . $user->id);
            return redirect()->back()->with('error', 'Akun Anda telah dinon-aktifkan karena terlalu banyak percobaan login. Hubungi admin untuk mengaktifkan kembali.');
        }


        // Autentikasi pengguna
        if (!Auth::attempt([$fieldType => $request->loginIdentifier, 'password' => $request->password])) {
            session()->put('login_attempts_' . $user->id, $attempts + 1);
            return back()->withErrors([
                'loginIdentifier' => "Email/ID-Card atau password salah. Percobaan ke-" . ($attempts + 1) . " dari 5."
            ])->withInput();
        }

        // Reset percobaan gagal setelah login berhasil
        session()->forget('login_attempts_' . $user->id);

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

    public function loginrfid(Request $request)
{
    // Validasi input RFID
    $request->validate([
        'RFID' => 'required|string',
    ]);

    // Cari user berdasarkan RFID
    $user = User::where('RFID', $request->RFID)->first();

    if (!$user) {
        return redirect()->back()->with('error', 'RFID tidak terdaftar.');
        // return redirect()->back()->withErrors(['RFID' => 'RFID tidak terdaftar.'])->withInput();
    }

    // Cek status akun, kecuali untuk superadmin
    if ($user->status == 0 && !$user->roles->pluck('name')->contains('superadmin')) {
        return redirect()->back()->with('error', 'Akun Anda telah dinon-aktifkan. Hubungi admin untuk mengaktifkan kembali.');
    }

    // Periksa jumlah percobaan gagal login dari session
    $attempts = session()->get('login_attempts_' . $user->id, 0);

    if ($attempts >= 5) {
        // Nonaktifkan akun jika sudah mencapai batas
        $user->update(['status' => 0]);
        session()->forget('login_attempts_' . $user->id);
        return redirect()->back()->with('error', 'Akun Anda telah dinon-aktifkan karena terlalu banyak percobaan login. Hubungi admin untuk mengaktifkan kembali.');
    }

    // Autentikasi manual tanpa password
    Auth::login($user);

    // Reset percobaan gagal setelah login berhasil
    session()->forget('login_attempts_' . $user->id);

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
    return redirect()->route('login-rfid')->withErrors(['error' => 'Role Anda tidak dikenali. Silakan hubungi administrator.']);
}


    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Logout berhasil!');
    }
}
