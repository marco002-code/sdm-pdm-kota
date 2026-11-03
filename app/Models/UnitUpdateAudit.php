<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UnitUpdateAudit extends Model
{
    use HasFactory;

    protected $table = 'unit_update_audits';

    protected $fillable = [
        'unit_id',
        'year',
        'quarter',
        'status',
        'last_updated_at',
        'updated_by',
        'verified_by',
        'total_employees',
        'notes',
    ];

    protected $casts = [
        'last_updated_at' => 'datetime',
        'year' => 'integer',
        'quarter' => 'integer',
        'total_employees' => 'integer',
    ];

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Hitung triwulan saat ini (1 s.d. 4).
     */
    public static function getCurrentQuarter(): int
    {
        $month = (int) date('n');
        return (int) ceil($month / 3);
    }
}
