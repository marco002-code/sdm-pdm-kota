<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    use HasFactory;

    // Status Presensi
    public const STATUS_HADIR = 'hadir';
    public const STATUS_TERLAMBAT = 'terlambat';
    public const STATUS_IZIN = 'izin';
    public const STATUS_SAKIT = 'sakit';
    public const STATUS_CUTI = 'cuti';
    public const STATUS_DINAS_LUAR = 'dinas_luar';
    public const STATUS_ALPA = 'alpa';

    protected $table = 'attendances';

    protected $fillable = [
        'employee_id',
        'unit_id',
        'date',
        'time_in',
        'time_out',
        'status',
        'overtime_hours',
        'check_in_latitude',
        'check_in_longitude',
        'check_in_photo',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
        'overtime_hours' => 'decimal:2',
        'check_in_latitude' => 'decimal:7',
        'check_in_longitude' => 'decimal:7',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function scopePresent($query)
    {
        return $query->whereIn('status', [self::STATUS_HADIR, self::STATUS_TERLAMBAT]);
    }

    public function scopeInUnit($query, int $unitId)
    {
        return $query->where('unit_id', $unitId);
    }

    public function scopeInDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }
}
