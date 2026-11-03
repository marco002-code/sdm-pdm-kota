<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaveRequest extends Model
{
    use HasFactory;

    // Status Pengajuan
    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED_PRINCIPAL = 'approved_by_principal';
    public const STATUS_APPROVED_HR = 'approved_by_hr';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_CANCELLED = 'cancelled';

    // Jenis Cuti
    public const TYPE_TAHUNAN = 'cuti_tahunan';
    public const TYPE_SAKIT = 'cuti_sakit';
    public const TYPE_MELAHIRKAN = 'cuti_melahirkan';
    public const TYPE_PENTING = 'cuti_alasan_penting';
    public const TYPE_IZIN = 'izin';
    public const TYPE_LAINNYA = 'lainnya';

    protected $table = 'leave_requests';

    protected $fillable = [
        'employee_id',
        'unit_id',
        'leave_type',
        'start_date',
        'end_date',
        'total_days',
        'reason',
        'attachment_path',
        'status',
        'principal_approver_id',
        'principal_approved_at',
        'principal_notes',
        'hr_approver_id',
        'hr_approved_at',
        'hr_notes',
        'rejected_by',
        'rejected_at',
        'rejection_reason',
        'deducts_leave_quota',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'total_days' => 'integer',
        'principal_approved_at' => 'datetime',
        'hr_approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'deducts_leave_quota' => 'boolean',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function principalApprover(): BelongsTo
    {
        return $this->belongsTo(User::class, 'principal_approver_id');
    }

    public function hrApprover(): BelongsTo
    {
        return $this->belongsTo(User::class, 'hr_approver_id');
    }

    public function rejectedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isApprovedByPrincipal(): bool
    {
        return $this->status === self::STATUS_APPROVED_PRINCIPAL;
    }

    public function isFullyApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED_HR;
    }

    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }
}
