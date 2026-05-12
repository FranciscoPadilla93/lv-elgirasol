<?php

namespace App\Models\School;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\User;
use App\Models\School\Expediente;
use App\Models\School\ExpedienteTutor;
use App\Models\Catalogs\Genero;

class Tutor extends Model
{
    use SoftDeletes;

    protected $table = 'tutores';

    protected $fillable = [
        'nombre',
        'apellido_paterno',
        'apellido_materno',
        'correo',
        'telefono',
        'telefono_secundario',
        'curp',
        'genero_id',
        'empresa',
        'puesto',
        'user_id',
        'observaciones',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [];
    }

    //RELACIONES
    public function expedientes(): BelongsToMany
    {
        return $this->belongsToMany(
            Expediente::class,
            'expediente_tutores'
        )
        ->withPivot([
            'parentesco_id',
            'is_primary_contact',
            'is_financial_responsible',
            'status',
        ])
        ->withTimestamps();
    }

    public function expedienteTutores(): HasMany
    {
        return $this->hasMany(ExpedienteTutor::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function genero(): BelongsTo
    {
        return $this->belongsTo(Genero::class);
    }

    // ACCESSORS
    public function getNombreCompletoAttribute(): string
    {
        return trim(
            "{$this->nombre} {$this->apellido_paterno} {$this->apellido_materno}"
        );
    }
}
