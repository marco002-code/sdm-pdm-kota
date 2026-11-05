<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Menampilkan form login resmi sistem.
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    /**
     * Memproses permintaan login standar.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember');

        // Cek login via email atau username
        $fieldType = filter_var($credentials['email'], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        if (Auth::attempt([$fieldType => $credentials['email'], 'password' => $credentials['password']], $remember)) {
            $request->session()->regenerate();

            // Catat last login
            /** @var User $user */
            $user = Auth::user();
            if ($user) {
                $user->update(['last_login_at' => now()]);
            }

            return redirect()->intended(route('dashboard'))
                ->with('success', 'Selamat datang kembali, ' . ($user->name ?? 'User') . '!');
        }

        throw ValidationException::withMessages([
            'email' => __('Email / username atau kata sandi yang Anda masukkan tidak sesuai.'),
        ]);
    }

    /**
     * Akses 1-Click Quick Login Akun Demo telah dinonaktifkan untuk keamanan sistem.
     */
    public function quickLogin(Request $request)
    {
        abort(403, 'Akses 1-click akun demo telah dinonaktifkan demi keamanan sistem kepegawaian.');
    }

    /**
     * Memproses keluar log (Logout).
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('info', 'Anda telah berhasil keluar dari sistem.');
    }
}
