<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ViceHeadmasterNomination extends Model
{
    protected $fillable = [
        'unit_id',
        'employee_id',
        'field',
        'period_years',
        'recommendation_headmaster',
        'status',
        'sk_number',
        'sk_date',
        'notes',
    ];

    protected $casts = [
        'sk_date' => 'date',
    ];

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function getFieldLabelAttribute()
    {
        return match ($this->field) {
            'kurikulum_ismuba'         => 'Waka Kurikulum & ISMUBA',
            'kesiswaan'                => 'Waka Kesiswaan',
            'sarpras'                  => 'Waka Sarana & Prasarana',
            'humas_kemuhammadiyahan'   => 'Waka Humas & Kemuhammadiyahan',
            default                    => ucfirst(str_replace('_', ' ', $this->field)),
        };
    }

    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            'diajukan'            => 'Diusulkan Kepala Sekolah',
            'verifikasi_majelis'  => 'Verifikasi Majelis Dikdasmen',
            'disetujui'           => 'Disetujui & Terbit SK PDM',
            'ditolak'             => 'Ditolak',
            default               => ucfirst($this->status),
        };
    }
}
