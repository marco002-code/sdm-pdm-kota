<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HeadmasterCandidate extends Model
{
    protected $fillable = [
        'headmaster_selection_id',
        'employee_id',
        'nbm',
        'vision_mission_rps',
        'recommendation_pcm',
        'recommendation_komite',
        'managerial_score',
        'supervision_score',
        'entrepreneurship_score',
        'aik_score',
        'presentation_score',
        'total_score',
        'pleno_decision',
        'is_elected',
        'sk_number',
        'sk_date',
        'inauguration_date',
    ];

    protected $casts = [
        'sk_date'                => 'date',
        'inauguration_date'      => 'date',
        'is_elected'             => 'boolean',
        'managerial_score'       => 'decimal:2',
        'supervision_score'      => 'decimal:2',
        'entrepreneurship_score' => 'decimal:2',
        'aik_score'              => 'decimal:2',
        'presentation_score'     => 'decimal:2',
        'total_score'            => 'decimal:2',
    ];

    public function selection()
    {
        return $this->belongsTo(HeadmasterSelection::class, 'headmaster_selection_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function calculateTotalScore()
    {
        $manajerial = (float) ($this->managerial_score ?? 0);
        $supervisi = (float) ($this->supervision_score ?? 0);
        $kewirausahaan = (float) ($this->entrepreneurship_score ?? 0);
        $aik = (float) ($this->aik_score ?? 0);
        $presentasi = (float) ($this->presentation_score ?? 0);

        // Bobot: Manajerial 25%, Supervisi 20%, Kewirausahaan 15%, AIK 20%, Pemaparan RPS 20%
        $total = ($manajerial * 0.25) + ($supervisi * 0.20) + ($kewirausahaan * 0.15) + ($aik * 0.20) + ($presentasi * 0.20);
        $this->total_score = round($total, 2);
        return $this->total_score;
    }
}
