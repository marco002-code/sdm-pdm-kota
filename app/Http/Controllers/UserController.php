<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Tampilkan daftar seluruh akun pengguna dengan filter & statistik akses.
     */
    public function index(Request $request)
    {
        $this->authorizeAccess();

        $query = User::with('unit')->orderBy('created_at', 'desc');

        // Pencarian (Nama, Username, Email, atau NPSN/Nama Unit)
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereHas('unit', function ($u) use ($search) {
                      $u->where('name', 'like', "%{$search}%")
                        ->orWhere('npsn', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                  });
            });
        }

        // Filter Role
        if ($request->filled('role')) {
            $role = $request->role;
            if ($role === 'admin') {
                $query->whereIn('role', [User::ROLE_ADMIN_UTAMA, User::ROLE_ADMIN]);
            } elseif ($role === 'hr_pusat') {
                $query->whereIn('role', [User::ROLE_ADMIN_YAYASAN, User::ROLE_IT_SUPPORT, User::ROLE_HR_PUSAT]);
            } elseif ($role === 'yayasan') {
                $query->whereIn('role', [User::ROLE_KETUA_MAJELIS, User::ROLE_KETUA_YAYASAN, User::ROLE_YAYASAN]);
            } elseif ($role === 'tu') {
                $query->whereIn('role', [User::ROLE_TU_UNIT, User::ROLE_TU, User::ROLE_TU_SEKOLAH]);
            } elseif ($role === 'kepsek') {
                $query->whereIn('role', [User::ROLE_KEPSEK, User::ROLE_KEPALA_SEKOLAH]);
            } elseif ($role === 'pegawai') {
                $query->where('role', User::ROLE_PEGAWAI);
            } else {
                $query->where('role', $role);
            }
        }

        // Filter Unit
        if ($request->filled('unit_id')) {
            $query->where('unit_id', $request->unit_id);
        }

        // Filter Status Aktif
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $users = $query->paginate(15)->withQueryString();

        // Statistik Ringkasan Pengguna
        $stats = [
            'total' => User::count(),
            'admin_pusat' => User::whereIn('role', [
                User::ROLE_ADMIN_UTAMA, User::ROLE_ADMIN,
                User::ROLE_ADMIN_YAYASAN, User::ROLE_IT_SUPPORT, User::ROLE_HR_PUSAT,
                User::ROLE_KETUA_MAJELIS, User::ROLE_KETUA_YAYASAN, User::ROLE_YAYASAN
            ])->count(),
            'tu_unit' => User::whereIn('role', [User::ROLE_TU_UNIT, User::ROLE_TU, User::ROLE_TU_SEKOLAH])->count(),
            'kepsek' => User::whereIn('role', [User::ROLE_KEPSEK, User::ROLE_KEPALA_SEKOLAH])->count(),
            'active' => User::where('is_active', true)->count(),
            'inactive' => User::where('is_active', false)->count(),
        ];

        $units = Unit::orderBy('name')->get();

        $rolesList = [
            User::ROLE_ADMIN => 'Admin Utama (Super Admin)',
            User::ROLE_HR_PUSAT => 'Admin Yayasan / SDM Pusat',
            User::ROLE_YAYASAN => 'Ketua Majelis Dikdasmen',
            User::ROLE_KEPSEK => 'Kepala Sekolah Unit',
            User::ROLE_TU => 'Petugas TU Unit (Operator Sekolah)',
            User::ROLE_PEGAWAI => 'Guru & Karyawan (ESS)',
        ];

        return view('users.index', compact('users', 'stats', 'units', 'rolesList'));
    }

    /**
     * Tambah akun pengguna baru (Add User).
     */
    public function store(Request $request)
    {
        $this->authorizeAccess();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:50', 'alpha_dash', 'unique:users,username'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', 'string'],
            'unit_id' => ['nullable', 'exists:units,id'],
            'password' => ['required', 'string', 'min:6'],
            'phone' => ['nullable', 'string', 'max:20'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'username.unique' => 'Username ini telah digunakan oleh pengguna lain.',
            'username.alpha_dash' => 'Username hanya boleh berupa huruf, angka, tanda strip, atau garis bawah.',
            'email.unique' => 'Alamat email ini sudah terdaftar di sistem.',
            'password.min' => 'Kata sandi minimal harus terdiri dari 6 karakter.',
        ]);

        $user = User::create([
            'name' => trim($validated['name']),
            'username' => strtolower(trim($validated['username'])),
            'email' => strtolower(trim($validated['email'])),
            'role' => $validated['role'],
            'unit_id' => $validated['unit_id'] ?? null,
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'] ?? null,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('users.index')
            ->with('success', "Akun pengguna baru '{$user->name}' ({$user->username}) berhasil ditambahkan ke dalam sistem.");
    }

    /**
     * Reset kata sandi pengguna (Reset Password).
     */
    public function resetPassword(Request $request, User $user)
    {
        $this->authorizeAccess();

        $validated = $request->validate([
            'new_password' => ['required', 'string', 'min:6'],
        ], [
            'new_password.required' => 'Kata sandi baru wajib diisi.',
            'new_password.min' => 'Kata sandi baru minimal harus terdiri dari 6 karakter.',
        ]);

        $user->update([
            'password' => Hash::make($validated['new_password']),
        ]);

        return redirect()->route('users.index')
            ->with('success', "Kata sandi untuk akun '{$user->name}' ({$user->username}) berhasil direset.");
    }

    /**
     * Toggle status aktif/nonaktif akun pengguna.
     */
    public function toggleStatus(Request $request, User $user)
    {
        $this->authorizeAccess();

        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')
                ->with('error', 'Pencegahan Keamanan: Anda tidak dapat menonaktifkan akun Anda sendiri yang sedang aktif.');
        }

        $user->update([
            'is_active' => !$user->is_active,
        ]);

        $statusText = $user->is_active ? 'diaktifkan kembali' : 'dinonaktifkan';

        return redirect()->route('users.index')
            ->with('success', "Status akun '{$user->name}' ({$user->username}) berhasil {$statusText}.");
    }

    /**
     * Hapus akun pengguna dengan dobel konfirmasi (Delete User).
     */
    public function destroy(Request $request, User $user)
    {
        $this->authorizeAccess();

        // Pencegahan 1: Dilarang menghapus akun sendiri
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')
                ->with('error', 'Pencegahan Keamanan: Anda tidak dapat menghapus akun Anda sendiri.');
        }

        // Pencegahan 2: Dilarang menghapus Admin Utama terakhir jika hanya tersisa 1
        if ($user->isAdminUtama() && User::whereIn('role', [User::ROLE_ADMIN_UTAMA, User::ROLE_ADMIN])->count() <= 1) {
            return redirect()->route('users.index')
                ->with('error', 'Pencegahan Keamanan: Tidak dapat menghapus Administrator Utama terakhir dalam sistem.');
        }

        // Validasi Dobel Konfirmasi (Wajib mengetikkan kata 'HAPUS')
        $request->validate([
            'confirm_delete' => ['required', 'string', 'in:HAPUS'],
        ], [
            'confirm_delete.required' => 'Konfirmasi penghapusan diperlukan.',
            'confirm_delete.in' => 'Kata kunci konfirmasi tidak sesuai. Harap ketik "HAPUS" dengan huruf kapital.',
        ]);

        $userName = $user->name;
        $userUsername = $user->username;

        // Lepaskan relasi employee agar data induk tidak ikut terhapus
        if ($user->employee) {
            $user->employee()->update(['user_id' => null]);
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', "Akun pengguna '{$userName}' ({$userUsername}) berhasil dihapus permanen dari sistem.");
    }

    /**
     * Verifikasi hak akses kelola akun (Admin Utama & Admin Yayasan).
     */
    private function authorizeAccess(): void
    {
        if (!auth()->check() || (!auth()->user()->isAdminUtama() && !auth()->user()->isAdminYayasan())) {
            abort(403, 'Akses terbatas: Menu Kelola Akun hanya dapat diakses oleh Administrator Utama dan Admin Yayasan.');
        }
    }
}

