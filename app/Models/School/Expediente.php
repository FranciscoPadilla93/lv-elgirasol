<?php

namespace App\Models\School;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Catalogs\Genero;
use App\Models\Catalogs\EstadoExpediente;
use App\Models\User;
use App\Models\School\Inscripcion;
use App\Models\School\ExpedienteTutor;
use App\Models\School\ExpedienteDocumento;
use App\Models\School\ExpedienteContacto;
use App\Models\Catalogs\CatTipoSeguroMedico;
use App\Models\Catalogs\CatGrupoSanguineo;


class Expediente extends Model
{
    use SoftDeletes;

    protected $table = 'expedientes';

    protected $fillable = [
        'folio',
        'nombre',
        'apellido_paterno',
        'apellido_materno',
        'fecha_nacimiento',
        'curp',
        'genero_id',
        'estado_expediente_id',
        'fecha_ingreso',
        'fecha_baja',
        'motivo_baja',
        'observaciones',
        'colonia',
        'otra_colonia',
        'calle',
        'numero_exterior',
        'numero_interior',
        'codigo_postal',
        'procedencia_academica',
        'tipo_escuela',
        'motivo_cambio',
        'alergias',
        'alergias_descripcion',
        'enfermedad_cronica',
        'enfermedad_cronica_descripcion',
        'grupo_sanguineo_id',
        'seguro_medico',
        'tipo_seguro_medico_id',
        'numero_poliza_seguro',
        'religion',
        'bautizado',
        'primera_comunion',
        'confirmado',
        'foto_path',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'fecha_nacimiento' => 'date',
            'fecha_ingreso' => 'date',
            'fecha_baja' => 'date',
            'alergias' => 'boolean',
            'enfermedad_cronica' => 'boolean',
            'seguro_medico' => 'boolean',
            'bautizado' => 'boolean',
            'primera_comunion' => 'boolean',
            'confirmado' => 'boolean',
        ];
    }

    // RELACIONES
    public function genero(): BelongsTo
    {
        return $this->belongsTo(Genero::class);
    }

    public function estadoExpediente(): BelongsTo
    {
        return $this->belongsTo(EstadoExpediente::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function documentos(): HasMany
    {
        return $this->hasMany(ExpedienteDocumento::class);
    }

    public function contactos(): HasMany
    {
        return $this->hasMany(ExpedienteContacto::class);
    }

    public function inscripciones(): HasMany
    {
        return $this->hasMany(Inscripcion::class);
    }

    public function tutores(): HasMany
    {
        return $this->hasMany(ExpedienteTutor::class);
    }

    public function tipoSeguroMedico(): BelongsTo
    {
        return $this->belongsTo(CatTipoSeguroMedico::class, 'tipo_seguro_medico_id');
    }

    public function grupoSanguineo(): BelongsTo
    {
        return $this->belongsTo(CatGrupoSanguineo::class, 'grupo_sanguineo_id');
    }

    public function getNombreCompletoAttribute(): string
    {
        return trim(
            "{$this->nombre} {$this->apellido_paterno} {$this->apellido_materno}"
        );
    }
}
