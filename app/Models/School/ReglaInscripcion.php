<?php

namespace App\Models\School;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Catalogs\Nivel;
use App\Models\Catalogs\Grado;
use App\Models\User;

class ReglaInscripcion extends Model
{
    protected $table = 'reglas_inscripcion';

    protected $fillable = [
        'nivel_id',
        'grado_id',
        // 'is_new_admission',
        'requires_evaluation',
        'requires_socioeconomic_study',
        'requires_treasury_validation',
        'required_documents',
        'required_evaluations',
        'minimum_score',
        'status',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            // 'is_new_admission' => 'boolean',
            'requires_socioeconomic_study' => 'boolean',
            'requires_treasury_validation' => 'boolean',
            'requires_evaluation' => 'boolean',
            'required_documents' => 'array',
            'required_evaluations' => 'array',
            'status' => 'boolean',
        ];
    }

    // RELACIONES
    public function nivel(): BelongsTo
    {
        return $this->belongsTo(Nivel::class);
    }

    public function grado(): BelongsTo
    {
        return $this->belongsTo(Grado::class);
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
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopeNewAdmission($query)
    {
        return $query->where('is_new_admission', true);
    }

    public function scopeReEnrollment($query)
    {
        return $query->where('is_new_admission', false);
    }
}
