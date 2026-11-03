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
     * Menampilkan form login beserta daftar akun demo untuk kemudahan pengujian.
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        $demoUsers = [
            [
                'role_key' => 'admin',
                'role_title' => 'IT Support (Admin)',
                'level' => 'Tingkat Pusat',
                'name' => 'IT Support Yayasan',
                'email' => 'admin@yayasan.sch.id',
                'password' => 'password',
                'badge_color' => 'bg-purple-100 text-purple-800 border-purple-200',
                'desc' => 'Manajemen sistem & akses'
            ],
            [
                'role_key' => 'ketua_yayasan',
                'role_title' => 'Ketua Yayasan',
                'level' => 'Tingkat Pusat',
                'name' => 'Dr. H. Ahmad Dahlan, M.Pd.',
                'email' => 'ketua@yayasan.sch.id',
                'password' => 'password',
                'badge_color' => 'bg-amber-100 text-amber-800 border-amber-200',
                'desc' => 'Dasbor eksekutif & monitoring'
            ],
            [
                'role_key' => 'hr_pusat',
                'role_title' => 'SDM Yayasan (HR Pusat)',
                'level' => 'Tingkat Pusat',
                'name' => 'Siti Nurhaliza, S.Psi.',
                'email' => 'hr@yayasan.sch.id',
                'password' => 'password',
                'badge_color' => 'bg-blue-100 text-blue-800 border-blue-200',
                'desc' => 'Full master data & approval pusat'
            ],
            [
                'role_key' => 'kepsek',
                'role_title' => 'Kepala Sekolah (SD)',
                'level' => 'Tingkat Unit',
                'name' => 'Dra. Endang Lestari, M.Pd.',
                'email' => 'kepsek.sd@sekolah.sch.id',
                'password' => 'password',
                'badge_color' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                'desc' => 'Approval unit SD & KPI staf'
            ],
            [
                'role_key' => 'tu',
                'role_title' => 'Tata Usaha (TU SD)',
                'level' => 'Tingkat Unit',
                'name' => 'Budi Setiawan, A.Md.',
                'email' => 'tu.sd@sekolah.sch.id',
                'password' => 'password',
                'badge_color' => 'bg-teal-100 text-teal-800 border-teal-200',
                'desc' => 'Rekapitulasi absen & payroll unit'
            ],
            [
                'role_key' => 'pegawai',
                'role_title' => 'Guru SD (ESS)',
                'level' => 'Tingkat Individu',
                'name' => 'Rahmat Hidayat, S.Pd.',
                'email' => 'rahmat.guru@sekolah.sch.id',
                'password' => 'password',
                'badge_color' => 'bg-sky-100 text-sky-800 border-sky-200',
                'desc' => 'Profil mandiri, presensi & cuti'
            ],
        ];

        $allTuUsers = User::with('unit')
            ->whereIn('role', [User::ROLE_TU, User::ROLE_TU_SEKOLAH])
            ->whereNotNull('unit_id')
            ->has('unit')
            ->get()
            ->sortBy(fn($u) => $u->unit->name)
            ->values();

        return view('auth.login', compact('demoUsers', 'allTuUsers'));
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
     * Fitur 1-Click Quick Login untuk Akun Demo.
     */
    public function quickLogin(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->with('error', 'Akun demo tidak ditemukan dalam database. Pastikan seeder sudah dijalankan.');
        }

        Auth::login($user);
        $request->session()->regenerate();
        $user->update(['last_login_at' => now()]);

        return redirect()->route('dashboard')
            ->with('success', 'Berhasil masuk sebagai demo ' . ucfirst(str_replace('_', ' ', $user->role)) . ' (' . $user->name . ')');
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
