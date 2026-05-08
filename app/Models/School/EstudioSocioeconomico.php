<?php

namespace App\Models\School;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class EstudioSocioeconomico extends Model
{
    use SoftDeletes;

    protected $table = 'estudios_socioeconomicos';

    protected $fillable = [
        'inscripcion_id',
        'approved_by',
        'submitted_by_tutor',
        'submitted_at',
        'responses',
        'is_approved',
        'approved_at',
        'approval_notes',
        'status',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'submitted_by_tutor' => 'boolean',
            'is_approved' => 'boolean',
            'submitted_at' => 'datetime',
            'approved_at' => 'datetime',
            'responses' => 'array',
            'status' => 'boolean',
        ];
    }

    // RELACIONES
    public function inscripcion(): BelongsTo
    {
        return $this->belongsTo(Inscripcion::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // SCOPES
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    public function scopePending($query)
    {
        return $query->where('is_approved', false);
    }
}
