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
            'nombre_completo' => $this->nombre_completo,
            'parentesco' => [
                'id' => $this->parentesco?->id,
                'code' => $this->parentesco?->code,
                'name' => $this->parentesco?->name,
            ],
            'tipo_contacto' => [
                'id' => $this->tipoContacto?->id,
                'code' => $this->tipoContacto?->code,
                'name' => $this->tipoContacto?->name,
            ],
            'telefono' => $this->telefono,
            'correo' => $this->correo,
            'uso_obligado' => $this->uso_obligado,
            'status' => $this->status,
            // FECHAS
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
