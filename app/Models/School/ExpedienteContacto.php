<?php

namespace App\Models\School;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Catalogs\Parentesco;
use App\Models\User;

class ExpedienteContacto extends Model
{
    use SoftDeletes;

    protected $table = 'expediente_contactos';

    protected $fillable = [
        'expediente_id',
        'parentesco_id',
        'nombre',
        'apellido_paterno',
        'apellido_materno',
        'telefono',
        'telefono_secundario',
        'correo',
        'is_emergency_contact',
        'is_authorized_pickup',
        'status',
        'observaciones',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'is_emergency_contact' => 'boolean',
            'is_authorized_pickup' => 'boolean',
            'status' => 'boolean',
        ];
    }

    // RELACIONES
    public function expediente(): BelongsTo
    {
        return $this->belongsTo(Expediente::class);
    }

    public function parentesco(): BelongsTo
    {
        return $this->belongsTo(Parentesco::class);
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

    // SCOPES
    public function scopeEmergency($query)
    {
        return $query->where('is_emergency_contact', true);
    }

    public function scopeAuthorizedPickup($query)
    {
        return $query->where('is_authorized_pickup', true);
    }
}
