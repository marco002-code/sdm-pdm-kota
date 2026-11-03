<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recruitment extends Model
{
    protected $fillable = [
        'title',
        'batch_code',
        'year',
        'start_date',
        'end_date',
        'test_date',
        'announcement_date',
        'target_employee_type',
        'positions',
        'requirements',
        'description',
        'status',
        'created_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
        'test_date'  => 'date',
        'announcement_date' => 'date',
        'positions'  => 'array',
    ];

    public function applicants()
    {
        return $this->hasMany(RecruitmentApplicant::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            'draft'              => 'Draft',
            'buka'               => 'Pendaftaran Dibuka',
            'seleksi_berkas'     => 'Seleksi Administrasi',
            'ujian_penjajagan'   => 'Ujian Penjajagan & Ibadah',
            'wawancara'          => 'Wawancara & Microteaching',
            'selesai'            => 'Seleksi Selesai',
            'ditutup'            => 'Ditutup',
            default              => ucfirst($this->status),
        };
    }
}
