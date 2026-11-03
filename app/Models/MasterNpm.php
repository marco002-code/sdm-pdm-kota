<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MasterNpm extends Model
{
    use HasFactory;

    protected $table = 'master_npms';

    protected $fillable = [
        'npm',
        'registered_name',
        'nbm',
        'allocated_unit_id',
        'is_used',
        'used_by_employee_id',
        'used_at',
        'notes',
    ];

    protected $casts = [
        'is_used' => 'boolean',
        'used_at' => 'datetime',
    ];

    public function allocatedUnit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'allocated_unit_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'used_by_employee_id');
    }

    /**
     * Validasi ketersediaan NPM: cek apakah ada di master dan belum digunakan orang lain.
     */
    public static function checkAvailability(string $npm, ?int $ignoreEmployeeId = null): array
    {
        $npm = trim($npm);
        $master = static::where('npm', $npm)->first();

        if (!$master) {
            return [
                'exists_in_master' => false,
                'is_valid' => true, // Masih dibolehkan jika master belum lengkap, tapi dengan status warning
                'message' => 'NPM ini belum terdaftar dalam Bank Data Master NPM PDM.',
                'warning' => true,
            ];
        }

        if ($master->is_used && $master->used_by_employee_id && $master->used_by_employee_id !== $ignoreEmployeeId) {
            $holder = $master->employee ? $master->employee->full_name : 'Pegawai lain';
            return [
                'exists_in_master' => true,
                'is_valid' => false,
                'message' => "NPM ini sudah terpakai oleh: {$holder}.",
                'warning' => false,
            ];
        }

        return [
            'exists_in_master' => true,
            'is_valid' => true,
            'message' => 'NPM valid dan terdaftar resmi di Bank Data PDM.',
            'registered_name' => $master->registered_name,
            'warning' => false,
        ];
    }
}
