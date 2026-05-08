<?php

namespace App\Models\School;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class EvaluacionInicial extends Model
{
    use SoftDeletes;

    protected $table = 'evaluaciones_iniciales';

    protected $fillable = [
        'inscripcion_id',
        'evaluated_by',
        'attempt',
        'evaluation_date',
        'score',
        'is_approved',
        'observaciones',
        'status',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'evaluation_date' => 'date',
            'score' => 'decimal:2',
            'is_approved' => 'boolean',
            'status' => 'boolean',
        ];
    }

    // RELACIONES
    public function inscripcion(): BelongsTo
    {
        return $this->belongsTo(Inscripcion::class);
    }

    public function evaluator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'evaluated_by');
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

    public function scopeRejected($query)
    {
        return $query->where('is_approved', false);
    }
}
