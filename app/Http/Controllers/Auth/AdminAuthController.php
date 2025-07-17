<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.admin.login');
    }

    public function login(LoginRequest $request)
    {
        $request->authenticate();

        // Cek apakah user adalah admin
        if (Auth::user()->role->name !== 'admin') {
            Auth::logout();
            return back()->withErrors([
                'email' => 'Anda tidak memiliki akses admin.',
            ]);
        }

        $request->session()->regenerate();

        // Tambahkan remember me
        if ($request->boolean('remember')) {
            Auth::guard('web')->setRememberDuration(7 * 24 * 60); // 7 hari
        }

        return redirect()->route('admin.admin-halaman-utama');
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
} 