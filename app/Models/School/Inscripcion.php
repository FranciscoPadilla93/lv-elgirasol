<?php

namespace App\Models\School;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Catalogs\Nivel;
use App\Models\Catalogs\Grado;
use App\Models\Catalogs\EstadoInscripcion;
use App\Models\User;
use App\Models\School\EvaluacionInicial;
use App\Models\School\EstudioSocioeconomico;

class Inscripcion extends Model
{
    use SoftDeletes;

    protected $table = 'inscripciones';

    protected $fillable = [
        'expediente_id',
        'ciclo_escolar_id',
        'nivel_id',
        'grado_id',
        'estado_inscripcion_id',
        'is_new_admission',
        'inscription_date',
        'requires_evaluation',
        'requires_socioeconomic_study',
        'requires_treasury_validation',
        'evaluation_approved',
        'socioeconomic_study_approved',
        'treasury_approved',
        'is_completed',
        'observaciones',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'is_new_admission' => 'boolean',
            'requires_evaluation' => 'boolean',
            'requires_socioeconomic_study' => 'boolean',
            'requires_treasury_validation' => 'boolean',
            'evaluation_approved' => 'boolean',
            'socioeconomic_study_approved' => 'boolean',
            'treasury_approved' => 'boolean',
            'is_completed' => 'boolean',
            'inscription_date' => 'datetime',
        ];
    }

    // RELACIONES
    public function expediente(): BelongsTo
    {
        return $this->belongsTo(Expediente::class);
    }

    public function nivel(): BelongsTo
    {
        return $this->belongsTo(Nivel::class);
    }

    public function grado(): BelongsTo
    {
        return $this->belongsTo(Grado::class);
    }

    public function estadoInscripcion(): BelongsTo
    {
        return $this->belongsTo(EstadoInscripcion::class);
    }

    public function evaluacionesIniciales(): HasMany
    {
        return $this->hasMany(EvaluacionInicial::class);
    }

    public function estudiosSocioeconomicos(): HasMany
    {
        return $this->hasMany(EstudioSocioeconomico::class);
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
    public function scopeCompleted($query)
    {
        return $query->where('is_completed', true);
    }

    public function scopePending($query)
    {
        return $query->where('is_completed', false);
    }
}
