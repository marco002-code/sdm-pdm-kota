<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeMutation extends Model
{
    use HasFactory;

    protected $table = 'employee_mutations';

    protected $fillable = [
        'employee_id',
        'origin_unit_id',
        'destination_unit_id',
        'origin_position',
        'destination_position',
        'effective_date',
        'sk_number',
        'sk_date',
        'reason',
        'status',
        'approved_by',
        'notes',
    ];

    protected $casts = [
        'effective_date' => 'date',
        'sk_date' => 'date',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function originUnit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'origin_unit_id');
    }

    public function destinationUnit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'destination_unit_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
