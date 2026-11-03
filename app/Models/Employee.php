<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use HasFactory, SoftDeletes;

    // Kategori Pegawai Sesuai PDM Kota Yogyakarta
    public const TYPE_PDM     = 'PDM';
    public const TYPE_GTY     = 'GTY';
    public const TYPE_GTT     = 'GTT';
    public const TYPE_PTY     = 'PTY';
    public const TYPE_PTT     = 'PTT';
    public const TYPE_LAINNYA = 'LAINNYA';

    // Legacy alias constants
    public const TYPE_GURU     = 'GTY';
    public const TYPE_TENDIK   = 'PTY';
    public const TYPE_KARYAWAN = 'PTT';

    public static function getCategories(): array
    {
        return [
            self::TYPE_PDM     => 'PDM (Majelis)',
            self::TYPE_GTY     => 'GTY (Guru Tetap Yayasan)',
            self::TYPE_GTT     => 'GTT (Guru Tidak Tetap)',
            self::TYPE_PTY     => 'PTY (Pegawai Tetap Yayasan)',
            self::TYPE_PTT     => 'PTT (Pegawai Tidak Tetap)',
            self::TYPE_LAINNYA => 'Lainnya',
        ];
    }

    // Status Kepegawaian
    public const STATUS_ACTIVE = 'aktif';
    public const STATUS_LEAVE = 'cuti';
    public const STATUS_INACTIVE = 'nonaktif';
    public const STATUS_MUTATION = 'mutasi';
    public const STATUS_RETIRED = 'pensiun';
    public const STATUS_DECEASED = 'wafat';

    public static function getStatusList(): array
    {
        return [
            self::STATUS_ACTIVE   => 'Aktif',
            self::STATUS_LEAVE    => 'Cuti',
            self::STATUS_MUTATION => 'Mutasi',
            self::STATUS_RETIRED  => 'Pensiun / Purna Tugas',
            self::STATUS_INACTIVE => 'Non-Aktif',
            self::STATUS_DECEASED => 'Wafat (Meninggal Dunia)',
        ];
    }

    protected $table = 'employees';

    protected $fillable = [
        // Akun & Unit
        'user_id',
        'unit_id',

        // 1. Data Umum & Identitas Multi-ID
        'npm', // Nomor Pegawai Muhammadiyah (ID Utama)
        'nik',
        'nip',
        'nbm',
        'simpatika_id',
        'full_name',
        'title_prefix',
        'title_suffix',
        'gender',
        'birth_place',
        'birth_date',
        'religion',
        'marital_status',
        'blood_type',
        'address',
        'phone_number',
        'email',
        'emergency_contact_name',
        'emergency_contact_phone',

        // Finansial & Rekening (Data Umum)
        'bank_name',
        'bank_account_number',
        'bank_account_holder',
        'npwp',
        'bpjs_ketenagakerjaan',
        'bpjs_kesehatan',

        // Status Kepegawaian & Gaji Pokok
        'employee_type',
        'employment_status',
        'position',
        'join_date',
        'end_date',
        'retirement_year',
        'status',
        'basic_salary',

        // 2. Khusus Guru
        'nuptk',
        'nrg',
        'teaching_subject',
        'is_certified_educator',
        'certification_year',
        'inpassing_status',
        'inpassing_grade',
        'teaching_history',

        // 3. Khusus Tenaga Kependidikan & Karyawan
        'work_unit_division',
        'work_shift',
        'technical_certifications',

        // Sertifikasi Ideologi Muhammadiyah (SIM)
        'sim_certificate_number',
        'sim_grade',
        'sim_certificate_file',
        'sim_date',

        // Pendidikan & Pengalaman
        'highest_education',
        'major',
        'university',
        'graduation_year',
        'education_history',
        'work_experiences',
        'organizational_experiences',

        // Rekam Jejak Prestasi & Penilaian
        'achievements',
        'dp3_scores',
        'pkks_scores',

        // Kuota Cuti
        'annual_leave_quota',
        'used_leave_quota',

        'notes',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'join_date' => 'date',
        'end_date' => 'date',
        'retirement_year' => 'integer',
        'sim_date' => 'date',
        'is_certified_educator' => 'boolean',
        'certification_year' => 'integer',
        'graduation_year' => 'integer',
        'annual_leave_quota' => 'integer',
        'used_leave_quota' => 'integer',
        'basic_salary' => 'decimal:2',
        'teaching_history' => 'array',
        'technical_certifications' => 'array',
        'education_history' => 'array',
        'work_experiences' => 'array',
        'organizational_experiences' => 'array',
        'achievements' => 'array',
        'dp3_scores' => 'array',
        'pkks_scores' => 'array',
    ];

    /* -------------------------------------------------------------------------- */
    /*                                RELATIONSHIPS                               */
    /* -------------------------------------------------------------------------- */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function masterNpm(): BelongsTo
    {
        return $this->belongsTo(MasterNpm::class, 'npm', 'npm');
    }

    public function mutations(): HasMany
    {
        return $this->hasMany(EmployeeMutation::class, 'employee_id')->orderBy('effective_date', 'desc');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(EmployeeDocument::class, 'employee_id');
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class, 'employee_id');
    }

    public function leaveRequests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class, 'employee_id');
    }

    public function performanceAppraisals(): HasMany
    {
        return $this->hasMany(PerformanceAppraisal::class, 'employee_id');
    }

    /* -------------------------------------------------------------------------- */
    /*                                   SCOPES                                   */
    /* -------------------------------------------------------------------------- */

    public function scopeTeachers($query)
    {
        return $query->whereIn('employee_type', [self::TYPE_GTY, self::TYPE_GTT, 'guru']);
    }

    public function scopeTendik($query)
    {
        return $query->whereIn('employee_type', [self::TYPE_PTY, self::TYPE_PTT, 'tendik']);
    }

    public function scopeStaff($query)
    {
        return $query->whereIn('employee_type', [self::TYPE_PTT, self::TYPE_LAINNYA, 'karyawan']);
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeRetired($query)
    {
        return $query->where('status', self::STATUS_RETIRED);
    }

    public function scopeWafat($query)
    {
        return $query->where('status', self::STATUS_DECEASED);
    }

    public function scopeInactiveOrDeceased($query)
    {
        return $query->whereIn('status', [self::STATUS_INACTIVE, self::STATUS_DECEASED, self::STATUS_RETIRED]);
    }

    public function scopeInUnit($query, int $unitId)
    {
        return $query->where('unit_id', $unitId);
    }

    public function scopeCertifiedTeachers($query)
    {
        return $query->whereIn('employee_type', [self::TYPE_GTY, self::TYPE_GTT, 'guru'])->where('is_certified_educator', true);
    }

    /* -------------------------------------------------------------------------- */
    /*                             ACCESSORS & HELPERS                            */
    /* -------------------------------------------------------------------------- */

    public function isTeacher(): bool
    {
        return in_array($this->employee_type, [self::TYPE_GTY, self::TYPE_GTT, 'guru']);
    }

    public function isTendik(): bool
    {
        return in_array($this->employee_type, [self::TYPE_PTY, self::TYPE_PTT, 'tendik']);
    }

    public function isStaff(): bool
    {
        return in_array($this->employee_type, [self::TYPE_PTT, self::TYPE_LAINNYA, 'karyawan']);
    }

    public function getCategoryLabelAttribute(): string
    {
        $cats = self::getCategories();
        return $cats[$this->employee_type] ?? ($this->employee_type ?: '-');
    }

    public function getRemainingLeaveQuotaAttribute(): int
    {
        return max(0, $this->annual_leave_quota - $this->used_leave_quota);
    }

    public function getFullNameWithTitlesAttribute(): string
    {
        $prefix = $this->title_prefix ? trim($this->title_prefix) . ' ' : '';
        $suffix = $this->title_suffix ? ', ' . trim($this->title_suffix) : '';
        return $prefix . $this->full_name . $suffix;
    }

    public function getIsActiveAttribute(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function isWafat(): bool
    {
        return $this->status === self::STATUS_DECEASED;
    }

    public function isRetired(): bool
    {
        return $this->status === self::STATUS_RETIRED;
    }

    public function getStatusLabelAttribute(): string
    {
        $list = self::getStatusList();
        return $list[$this->status] ?? ucfirst($this->status ?: 'Aktif');
    }

    /**
     * Hitung perkiraan Tahun Batas Usia Pensiun (BUP) berdasarkan tanggal lahir & regulasi PDM:
     * - Guru: 60 tahun
     * - Karyawan / Tendik: 58 tahun
     */
    public function calculateRetirementYear(): ?int
    {
        if ($this->retirement_year) {
            return (int) $this->retirement_year;
        }

        if (!$this->birth_date) {
            return null;
        }

        $birthYear = (int) $this->birth_date->format('Y');
        $pensionAge = $this->isTeacher() ? 60 : 58;

        return $birthYear + $pensionAge;
    }

    protected static function booted(): void
    {
        static::saving(function ($employee) {
            // Auto-hitung tahun pensiun jika kosong dan tanggal lahir ada
            if (empty($employee->retirement_year) && !empty($employee->birth_date)) {
                $employee->retirement_year = $employee->calculateRetirementYear();
            }

            // Jika status wafat, nonaktif, atau pensiun, nonaktifkan akun user login terkait
            if (in_array($employee->status, [self::STATUS_DECEASED, self::STATUS_INACTIVE, self::STATUS_RETIRED]) && $employee->user_id) {
                User::where('id', $employee->user_id)->update(['is_active' => false]);
            }
        });
    }
}