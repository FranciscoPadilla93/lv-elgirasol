<?php

namespace App\Http\Resources\School;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExpedienteContactoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'apellido_paterno' => $this->apellido_paterno,
            'apellido_materno' => $this->apellido_materno,
            'nombre_completo' => trim(
                "{$this->nombre} {$this->apellido_paterno} {$this->apellido_materno}"
            ),
            'parentesco' => [
                'id' => $this->parentesco?->id,
                'code' => $this->parentesco?->code,
                'name' => $this->parentesco?->name,
            ],
            // CONTACTO
            'telefono' => $this->telefono,
            'telefono_secundario' => $this->telefono_secundario,
            'correo' => $this->correo,
            // CONFIGURACIÓN
            'is_emergency_contact' => $this->is_emergency_contact,
            'is_authorized_pickup' => $this->is_authorized_pickup,
            'status' => $this->status,
            // OBSERVACIONES
            'observaciones' => $this->observaciones,
            // FECHAS
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
