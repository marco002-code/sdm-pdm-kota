<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    protected $fillable = [
        'code',
        'npsn',
        'name',
        'level',
        'district',
        'village',
        'accreditation',
        'website',
        'address',
        'phone',
        'email',
        'principal_name',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public const LEVEL_YAYASAN = 'YAYASAN';
    public const LEVEL_TPA     = 'TPA';
    public const LEVEL_TK      = 'TK';
    public const LEVEL_SD      = 'SD';
    public const LEVEL_SMP     = 'SMP';
    public const LEVEL_MTS     = 'MTS';
    public const LEVEL_SMA     = 'SMA';
    public const LEVEL_SMK     = 'SMK';
    public const LEVEL_MA      = 'MA';
    public const LEVEL_PKBM    = 'PKBM';
    public const LEVEL_LAINNYA = 'LAINNYA';

    public static function getLevels(): array
    {
        return [
            self::LEVEL_YAYASAN => 'Kantor PDM / Yayasan',
            self::LEVEL_TPA     => 'TPA / KB',
            self::LEVEL_TK      => 'TK / ABA',
            self::LEVEL_SD      => 'SD / MI',
            self::LEVEL_SMP     => 'SMP Muhammadiyah',
            self::LEVEL_MTS     => 'MTs Muhammadiyah',
            self::LEVEL_SMA     => 'SMA Muhammadiyah',
            self::LEVEL_SMK     => 'SMK Muhammadiyah',
            self::LEVEL_MA      => 'MA Muhammadiyah',
            self::LEVEL_PKBM    => 'PKBM Muhammadiyah',
            self::LEVEL_LAINNYA => 'Lainnya',
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeLevel($query, string $level)
    {
        return $query->where('level', $level);
    }

    public function users()
    {
        return $this->hasMany(User::class, 'unit_id');
    }

    public function employees()
    {
        return $this->hasMany(Employee::class, 'unit_id');
    }

    public function mutationsOrigin()
    {
        return $this->hasMany(EmployeeMutation::class, 'origin_unit_id');
    }

    public function mutationsDestination()
    {
        return $this->hasMany(EmployeeMutation::class, 'destination_unit_id');
    }

    public function updateAudits()
    {
        return $this->hasMany(UnitUpdateAudit::class, 'unit_id');
    }
}