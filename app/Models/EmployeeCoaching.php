<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeCoaching extends Model
{
    use HasFactory;

    protected $table = 'employee_coachings';

    protected $fillable = [
        'employee_id',
        'unit_id',
        'coaching_date',
        'category',
        'title',
        'organizer',
        'notes',
        'follow_up',
        'status',
        'created_by',
    ];

    protected $casts = [
        'coaching_date' => 'date',
    ];

    public const CATEGORY_IDEOLOGI   = 'ideologi_aik';
    public const CATEGORY_LOYALITAS  = 'komitmen_loyalitas';
    public const CATEGORY_PELATIHAN  = 'kompetensi_pelatihan';
    public const CATEGORY_DISIPLIN   = 'disiplin_sanksi';
    public const CATEGORY_LAINNYA    = 'lainnya';

    public static function getCategories(): array
    {
        return [
            self::CATEGORY_IDEOLOGI   => 'Ideologi Al-Islam & Kemuhammadiyahan',
            self::CATEGORY_LOYALITAS  => 'Komitmen & Loyalitas Persyarikatan',
            self::CATEGORY_PELATIHAN  => 'Pelatihan & Peningkatan Kompetensi',
            self::CATEGORY_DISIPLIN   => 'Pembinaan Disiplin & Tata Tertib',
            self::CATEGORY_LAINNYA    => 'Lainnya',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
