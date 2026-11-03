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

    // Definisi Konstanta Role sesuai Rancangan RBAC
    public const ROLE_ADMIN = 'admin';                 // IT Support Yayasan
    public const ROLE_YAYASAN = 'yayasan';             // Eksekutif Yayasan
    public const ROLE_KETUA_YAYASAN = 'ketua_yayasan'; // Eksekutif Yayasan
    public const ROLE_HR_PUSAT = 'hr_pusat';           // SDM Yayasan / HR Pusat
    public const ROLE_KEPSEK = 'kepsek';               // Kepala Sekolah Unit
    public const ROLE_KEPALA_SEKOLAH = 'kepala_sekolah';// Kepala Sekolah Unit (TK/SD/SMP/SMA)
    public const ROLE_TU = 'tu';                       // Tata Usaha Sekolah
    public const ROLE_TU_SEKOLAH = 'tu_sekolah';       // Tata Usaha Sekolah
    public const ROLE_PEGAWAI = 'pegawai';             // Guru & Karyawan (ESS)

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
        'last_login_at' => 'datetime',
        'is_active' => 'boolean',
        'password' => 'hashed',
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

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isYayasan(): bool
    {
        return in_array($this->role, [self::ROLE_YAYASAN, self::ROLE_KETUA_YAYASAN], true);
    }

    public function isHrPusat(): bool
    {
        return $this->role === self::ROLE_HR_PUSAT;
    }

    public function isKepalaSekolah(): bool
    {
        return in_array($this->role, [self::ROLE_KEPSEK, self::ROLE_KEPALA_SEKOLAH], true);
    }

    public function isTuSekolah(): bool
    {
        return in_array($this->role, [self::ROLE_TU, self::ROLE_TU_SEKOLAH], true);
    }

    public function isTu(): bool
    {
        return $this->isTuSekolah();
    }

    public function isPegawai(): bool
    {
        return $this->role === self::ROLE_PEGAWAI;
    }

    /**
     * Pengguna dengan hak akses tingkat yayasan (pusat).
     */
    public function isYayasanLevel(): bool
    {
        return in_array($this->role, [
            self::ROLE_ADMIN,
            self::ROLE_YAYASAN,
            self::ROLE_KETUA_YAYASAN,
            self::ROLE_HR_PUSAT,
        ], true);
    }

    /**
     * Pengguna dengan hak akses tingkat unit sekolah.
     */
    public function isUnitLevel(): bool
    {
        return in_array($this->role, [
            self::ROLE_KEPSEK,
            self::ROLE_KEPALA_SEKOLAH,
            self::ROLE_TU,
            self::ROLE_TU_SEKOLAH,
            self::ROLE_PEGAWAI,
        ], true);
    }

    /**
     * Memeriksa apakah pengguna memiliki salah satu role yang diberikan.
     */
    public function hasRole(string|array $roles): bool
    {
        if (is_array($roles)) {
            return in_array($this->role, $roles, true);
        }

        return $this->role === $roles;
    }

    /**
     * Scope pengguna aktif.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}