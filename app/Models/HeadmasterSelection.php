<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HeadmasterSelection extends Model
{
    protected $fillable = [
        'unit_id',
        'period_years',
        'announcement_date',
        'closing_date',
        'fit_proper_date',
        'pleno_date',
        'status',
        'notes',
    ];

    protected $casts = [
        'announcement_date' => 'date',
        'closing_date'      => 'date',
        'fit_proper_date'   => 'date',
        'pleno_date'        => 'date',
    ];

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function candidates()
    {
        return $this->hasMany(HeadmasterCandidate::class);
    }

    public function electedCandidate()
    {
        return $this->hasOne(HeadmasterCandidate::class)->where('is_elected', true);
    }

    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            'penjaringan'        => 'Penjaringan Bakal Calon',
            'verifikasi_berkas'  => 'Verifikasi Berkas & Portofolio',
            'fit_and_proper'     => 'Fit & Proper Test',
            'sidang_pleno'       => 'Sidang Pleno Majelis & PDM',
            'penetapan_sk'       => 'Penetapan & Penerbitan SK',
            'selesai'            => 'Selesai Dilantik',
            default              => ucfirst(str_replace('_', ' ', $this->status)),
        };
    }
}
