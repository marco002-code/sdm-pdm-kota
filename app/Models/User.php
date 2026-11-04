<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // --------------------------------------------------------------------------
    // Definisi Baku Konstanta Role & Alias RBAC PDM Kota Yogyakarta
    // --------------------------------------------------------------------------
    public const ROLE_ADMIN_UTAMA   = 'admin_utama';    // 1. Admin Utama (Super Admin)
    public const ROLE_ADMIN         = 'admin';          // Alias Admin Utama
    public const ROLE_ADMIN_YAYASAN = 'admin_yayasan';  // 2. Admin Yayasan / SDM Yayasan
    public const ROLE_IT_SUPPORT    = 'it_support';     // 2b. IT Support Yayasan
    public const ROLE_HR_PUSAT      = 'hr_pusat';       // Alias Admin Yayasan (HR Pusat)
    public const ROLE_KETUA_MAJELIS = 'ketua_majelis';  // 3. Ketua Majelis (Read-Only All + Approval)
    public const ROLE_KETUA_YAYASAN = 'ketua_yayasan';  // Alias Ketua Majelis
    public const ROLE_YAYASAN       = 'yayasan';        // Alias Eksekutif Yayasan
    public const ROLE_TU_UNIT       = 'tu_unit';        // 4. Petugas TU Unit (Operator Sekolah)
    public const ROLE_TU            = 'tu';             // Alias Petugas TU Unit
    public const ROLE_TU_SEKOLAH    = 'tu_sekolah';     // Alias Petugas TU Unit
    public const ROLE_KEPSEK        = 'kepsek';         // Kepala Sekolah Unit
    public const ROLE_KEPALA_SEKOLAH= 'kepala_sekolah'; // Alias Kepala Sekolah Unit
    public const ROLE_PEGAWAI       = 'pegawai';        // Guru & Karyawan (ESS)

    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
        'role',
        'unit_id',
        'phone',
        'avatar',
        'is_active',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at'     => 'datetime',
        'is_active'         => 'boolean',
        'password'          => 'hashed',
    ];

    /**
     * Relasi ke Unit Sekolah (khusus peran level unit).
     */
    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    /**
     * Relasi 1-to-1 ke profil data induk pegawai.
     */
    public function employee(): HasOne
    {
        return $this->hasOne(Employee::class, 'user_id');
    }

    /* -------------------------------------------------------------------------- */
    /*                                RBAC HELPERS                                */
    /* -------------------------------------------------------------------------- */

    /**
     * 1. Admin Utama (Super Admin) - Akses penuh seluruh sistem, CRUD & Delete.
     */
    public function isAdminUtama(): bool
    {
        return in_array($this->role, [self::ROLE_ADMIN_UTAMA, self::ROLE_ADMIN], true);
    }

    public function isAdmin(): bool
    {
        return $this->isAdminUtama();
    }

    /**
     * 2. Admin Yayasan / IT Support Yayasan - Pengelolaan operasional data yayasan & sistem.
     */
    public function isAdminYayasan(): bool
    {
        return in_array($this->role, [self::ROLE_ADMIN_YAYASAN, self::ROLE_IT_SUPPORT, self::ROLE_HR_PUSAT], true);
    }

    public function isHrPusat(): bool
    {
        return $this->isAdminYayasan();
    }

    /**
     * 3. Ketua Majelis - Read All Menu, tidak bisa edit atau hapus, KECUALI yang approve butuh Ketua Majelis.
     */
    public function isKetuaMajelis(): bool
    {
        return in_array($this->role, [self::ROLE_KETUA_MAJELIS, self::ROLE_KETUA_YAYASAN, self::ROLE_YAYASAN], true);
    }

    public function isYayasan(): bool
    {
        return $this->isKetuaMajelis();
    }

    /**
     * 4. Petugas TU Unit (Operator Sekolah) - Kelola data pegawai & usulan di unitnya sendiri.
     */
    public function isTuUnit(): bool
    {
        return in_array($this->role, [self::ROLE_TU_UNIT, self::ROLE_TU, self::ROLE_TU_SEKOLAH], true);
    }

    public function isTuSekolah(): bool
    {
        return $this->isTuUnit();
    }

    public function isTu(): bool
    {
        return $this->isTuUnit();
    }

    public function isKepalaSekolah(): bool
    {
        return in_array($this->role, [self::ROLE_KEPSEK, self::ROLE_KEPALA_SEKOLAH], true);
    }

    public function isPegawai(): bool
    {
        return $this->role === self::ROLE_PEGAWAI;
    }

    /**
     * Pengguna dengan hak akses tingkat yayasan / PDM (pusat).
     */
    public function isYayasanLevel(): bool
    {
        return $this->isAdminUtama() || $this->isAdminYayasan() || $this->isKetuaMajelis();
    }

    /**
     * Pengguna dengan hak akses tingkat unit sekolah.
     */
    public function isUnitLevel(): bool
    {
        return $this->isKepalaSekolah() || $this->isTuUnit() || $this->isPegawai();
    }

    /* -------------------------------------------------------------------------- */
    /*                         CAPABILITY & PERMISSIONS                           */
    /* -------------------------------------------------------------------------- */

    /**
     * Hak persetujuan / verifikasi keputusan Majelis (Sidang Majelis, SK PDM, SK Waka).
     * Sesuai ketentuan: wewenang Ketua Majelis dan Admin Utama.
     */
    public function canApproveAsKetuaMajelis(): bool
    {
        return $this->isKetuaMajelis() || $this->isAdminUtama();
    }

    /**
     * Hak hapus data permanen / soft delete (Hanya Admin Utama).
     * Ketua Majelis, Admin Yayasan, dan Petugas TU TIDAK memiliki hak hapus data pegawai.
     */
    public function canDeleteEmployees(): bool
    {
        return $this->isAdminUtama();
    }

    /**
     * Hak penambahan / pengubahan data induk pegawai.
     * Ketua Majelis DILARANG mengedit/menambah data pegawai (hanya meninjau).
     */
    public function canEditEmployees(): bool
    {
        if ($this->isKetuaMajelis()) {
            return false;
        }

        return $this->isAdminUtama() || $this->isAdminYayasan() || $this->isTuUnit() || $this->isKepalaSekolah();
    }

    /**
     * Hak pengelolaan Bank Data Master NPM pusat.
     */
    public function canManageMasterNpm(): bool
    {
        return $this->isAdminUtama() || $this->isAdminYayasan();
    }

    /**
     * Hak pencatatan mutasi pegawai antar unit.
     */
    public function canManageMutations(): bool
    {
        return $this->isAdminUtama() || $this->isAdminYayasan();
    }

    /**
     * Hak akses pengelolaan pemeliharaan sistem (Maintenance Mode, Clear Cache, System Diagnostics).
     * Khusus Admin Utama dan IT Support / Admin Yayasan.
     */
    public function canManageMaintenance(): bool
    {
        return $this->isAdminUtama() || $this->isAdminYayasan();
    }

    /**
     * Memeriksa apakah pengguna memiliki salah satu role yang diberikan (mendukung alias).
     */
    public function hasRole(string|array $roles): bool
    {
        if (!is_array($roles)) {
            $roles = [$roles];
        }

        foreach ($roles as $r) {
            if ($r === 'admin_utama' && $this->isAdminUtama()) return true;
            if ($r === 'admin' && $this->isAdminUtama()) return true;
            if ($r === 'admin_yayasan' && $this->isAdminYayasan()) return true;
            if ($r === 'it_support' && $this->isAdminYayasan()) return true;
            if ($r === 'hr_pusat' && $this->isAdminYayasan()) return true;
            if ($r === 'ketua_majelis' && $this->isKetuaMajelis()) return true;
            if ($r === 'ketua_yayasan' && $this->isKetuaMajelis()) return true;
            if ($r === 'yayasan' && $this->isKetuaMajelis()) return true;
            if ($r === 'tu_unit' && $this->isTuUnit()) return true;
            if ($r === 'tu' && $this->isTuUnit()) return true;
            if ($r === 'tu_sekolah' && $this->isTuUnit()) return true;
            if ($r === 'kepsek' && $this->isKepalaSekolah()) return true;
            if ($r === 'kepala_sekolah' && $this->isKepalaSekolah()) return true;
            if ($r === 'pegawai' && $this->isPegawai()) return true;
            if ($this->role === $r) return true;
        }

        return false;
    }

    /**
     * Judul / Label Tampilan Peran Pengguna.
     */
    public function getRoleTitleAttribute(): string
    {
        if ($this->isAdminUtama()) return 'Admin Utama';
        if ($this->isAdminYayasan()) return 'Admin Yayasan / IT Support';
        if ($this->isKetuaMajelis()) return 'Ketua Majelis';
        if ($this->isTuUnit()) return 'Petugas TU Unit';
        if ($this->isKepalaSekolah()) return 'Kepala Sekolah';
        if ($this->isPegawai()) return 'Guru / Pegawai';

        return ucfirst(str_replace('_', ' ', $this->role ?? 'Pengguna'));
    }

    /**
     * Warna Lencana Peran Pengguna.
     */
    public function getRoleBadgeColorAttribute(): string
    {
        if ($this->isAdminUtama()) return 'bg-purple-100 text-purple-800 border-purple-200';
        if ($this->isAdminYayasan()) return 'bg-blue-100 text-blue-800 border-blue-200';
        if ($this->isKetuaMajelis()) return 'bg-amber-100 text-amber-800 border-amber-200';
        if ($this->isTuUnit()) return 'bg-teal-100 text-teal-800 border-teal-200';
        if ($this->isKepalaSekolah()) return 'bg-emerald-100 text-emerald-800 border-emerald-200';

        return 'bg-slate-100 text-slate-800 border-slate-200';
    }

    /**
     * Scope pengguna aktif.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
