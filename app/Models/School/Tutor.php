<?php

namespace App\Models\School;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;
use App\Models\School\ExpedienteTutor;

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
        'empresa',
        'puesto',
        'user_id',
        'observaciones',
        'created_by',
        'updated_by',
    ];

    //RELACIONES
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

    // ACCESSORS
    public function getNombreCompletoAttribute(): string
    {
        return trim(
            "{$this->nombre} {$this->apellido_paterno} {$this->apellido_materno}"
        );
    }
}
