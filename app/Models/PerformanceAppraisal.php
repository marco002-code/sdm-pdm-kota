<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PerformanceAppraisal extends Model
{
    use HasFactory;

    // Status Penilaian
    public const STATUS_DRAFT = 'draft';
    public const STATUS_SUBMITTED = 'submitted';
    public const STATUS_REVIEWED_HR = 'reviewed_by_hr';
    public const STATUS_FINALIZED = 'finalized';

    protected $table = 'performance_appraisals';

    protected $fillable = [
        'employee_id',
        'evaluator_id',
        'unit_id',
        'period',
        'appraisal_date',
        'pedagogic_score',
        'discipline_score',
        'technical_competency_score',
        'attitude_score',
        'final_score',
        'grade',
        'strengths',
        'improvements_needed',
        'notes',
        'kpi_details',
        'status',
    ];

    protected $casts = [
        'appraisal_date' => 'date',
        'pedagogic_score' => 'decimal:2',
        'discipline_score' => 'decimal:2',
        'technical_competency_score' => 'decimal:2',
        'attitude_score' => 'decimal:2',
        'final_score' => 'decimal:2',
        'kpi_details' => 'array',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function evaluator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'evaluator_id');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function calculateFinalScore(bool $isTeacher = true): float
    {
        if ($isTeacher) {
            // Bobot Guru: Pedagogik (40%), Disiplin (20%), Kompetensi (20%), Sikap (20%)
            $this->final_score = round(
                (($this->pedagogic_score ?? 0) * 0.40) +
                (($this->discipline_score ?? 0) * 0.20) +
                (($this->technical_competency_score ?? 0) * 0.20) +
                (($this->attitude_score ?? 0) * 0.20),
                2
            );
        } else {
            // Bobot Karyawan: Teknis/Tugas (40%), Disiplin (30%), Sikap (30%)
            $this->final_score = round(
                (($this->technical_competency_score ?? 0) * 0.40) +
                (($this->discipline_score ?? 0) * 0.30) +
                (($this->attitude_score ?? 0) * 0.30),
                2
            );
        }

        // Tentukan Predikat / Grade
        if ($this->final_score >= 90) {
            $this->grade = 'A';
        } elseif ($this->final_score >= 80) {
            $this->grade = 'B';
        } elseif ($this->final_score >= 70) {
            $this->grade = 'C';
        } elseif ($this->final_score >= 60) {
            $this->grade = 'D';
        } else {
            $this->grade = 'E';
        }

        return (float) $this->final_score;
    }
}
