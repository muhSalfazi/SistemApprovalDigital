<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo($request)
    {
        if (!$request->expectsJson()) {
            // Tambahkan pesan ke session flash
            session()->flash('alert', [
                'type' => 'warning', // Tipe alert (success, error, warning, info)
                'message' => 'Sesi Anda telah berakhir. Silakan login kembali.', // Pesan yang akan ditampilkan
            ]);
            return route('login');
        }
    }


}
