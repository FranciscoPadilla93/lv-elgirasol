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

    public function expedienteTutores(): HasMany
    {
        return $this->hasMany(ExpedienteTutor::class);
    }
}
