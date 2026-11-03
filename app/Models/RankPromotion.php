<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RankPromotion extends Model
{
    protected $fillable = [
        'employee_id',
        'unit_id',
        'promotion_type',
        'current_rank',
        'proposed_rank',
        'current_employee_type',
        'proposed_employee_type',
        'years_of_service',
        'performance_score',
        'credit_points',
        'new_education_degree',
        'ortom_involvement',
        'supporting_documents',
        'status',
        'reviewer_notes',
        'sk_number',
        'sk_date',
        'effective_date',
        'verified_by',
    ];

    protected $casts = [
        'sk_date'              => 'date',
        'effective_date'       => 'date',
        'performance_score'    => 'decimal:2',
        'credit_points'        => 'decimal:2',
        'supporting_documents' => 'array',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function getPromotionTypeLabelAttribute()
    {
        return match ($this->promotion_type) {
            'reguler_berkala'    => 'Kenaikan Pangkat Reguler / Berkala',
            'penyesuaian_ijazah' => 'Penyesuaian Ijazah Baru',
            'alih_status_gty'    => 'Alih Status Guru (GTT ke GTY)',
            'alih_status_pty'    => 'Alih Status Pegawai (PTT ke PTY)',
            'istimewa'           => 'Kenaikan Pangkat Istimewa / Prestasi',
            default              => ucfirst(str_replace('_', ' ', $this->promotion_type)),
        };
    }

    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            'diajukan'             => 'Diajukan Pegawai',
            'diverifikasi_sekolah' => 'Diverifikasi Unit / Kepala Sekolah',
            'sidang_majelis'       => 'Proses Sidang Tim Penilai Majelis',
            'disetujui_pdm'        => 'Disetujui & Terbit SK PDM',
            'ditolak'              => 'Ditolak / Belum Memenuhi Syarat',
            default                => ucfirst($this->status),
        };
    }
}
