<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecruitmentApplicant extends Model
{
    protected $fillable = [
        'recruitment_id',
        'registration_number',
        'nik',
        'nbm',
        'full_name',
        'gender',
        'birth_place',
        'birth_date',
        'email',
        'phone_number',
        'highest_education',
        'major',
        'university',
        'gpa',
        'applied_position',
        'preferred_unit_id',
        'placed_unit_id',
        'employee_type',
        'documents',
        'administration_status',
        'administration_notes',
        'written_test_score',
        'aik_test_score',
        'worship_test_score',
        'interview_score',
        'microteaching_score',
        'final_score',
        'selection_status',
        'sk_number',
        'sk_date',
        'created_employee_id',
    ];

    protected $casts = [
        'birth_date'          => 'date',
        'sk_date'             => 'date',
        'documents'           => 'array',
        'gpa'                 => 'decimal:2',
        'written_test_score'  => 'decimal:2',
        'aik_test_score'      => 'decimal:2',
        'worship_test_score'  => 'decimal:2',
        'interview_score'     => 'decimal:2',
        'microteaching_score' => 'decimal:2',
        'final_score'         => 'decimal:2',
    ];

    public function recruitment()
    {
        return $this->belongsTo(Recruitment::class);
    }

    public function preferredUnit()
    {
        return $this->belongsTo(Unit::class, 'preferred_unit_id');
    }

    public function placedUnit()
    {
        return $this->belongsTo(Unit::class, 'placed_unit_id');
    }

    public function createdEmployee()
    {
        return $this->belongsTo(Employee::class, 'created_employee_id');
    }

    public function calculateFinalScore()
    {
        $written = (float) ($this->written_test_score ?? 0);
        $aik = (float) ($this->aik_test_score ?? 0);
        $worship = (float) ($this->worship_test_score ?? 0);
        $interview = (float) ($this->interview_score ?? 0);
        $micro = (float) ($this->microteaching_score ?? 0);

        // Bobot: Tertulis 20%, AIK 25%, Praktik Ibadah 25%, Wawancara 15%, Microteaching 15%
        $score = ($written * 0.20) + ($aik * 0.25) + ($worship * 0.25) + ($interview * 0.15) + ($micro * 0.15);
        $this->final_score = round($score, 2);
        return $this->final_score;
    }
}
