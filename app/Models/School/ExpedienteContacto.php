<?php

namespace App\Models\School;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Catalogs\Parentesco;
use App\Models\User;
use App\Models\Catalogs\TipoContacto;

class ExpedienteContacto extends Model
{
    use SoftDeletes;

    protected $table = 'expediente_contactos';

    protected $fillable = [
        'expediente_id',
        'parentesco_id',
        'tipo_contacto_id',
        'nombre_completo',
        'telefono',
        'correo',
        'uso_obligado',
        'status',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'uso_obligado' => 'boolean',
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

    public function tipoContacto(): BelongsTo
    {
        return $this->belongsTo(TipoContacto::class, 'tipo_contacto_id');
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
