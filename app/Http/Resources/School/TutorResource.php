<?php

namespace App\Http\Resources\School;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TutorResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            // IDENTIFICACIÓN
            'id' => $this->id,
            // DATOS PERSONALES
            'nombre' => $this->nombre,
            'apellido_paterno' => $this->apellido_paterno,
            'apellido_materno' => $this->apellido_materno,
            'nombre_completo' => $this->nombre_completo,
            'curp' => $this->curp,
            // GÉNERO
            'genero' => $this->whenLoaded('genero', function () {
                return [
                    'id' => $this->genero->id,
                    'code' => $this->genero->code,
                    'name' => $this->genero->name,
                ];
            }),
            // CONTACTO
            'telefono' => $this->telefono,
            'telefono_secundario' => $this->telefono_secundario,
            'correo' => $this->correo,
            // INFORMACIÓN LABORAL
            'empresa' => $this->empresa,
            'puesto' => $this->puesto,
            // INTRANET
            'user' => $this->whenLoaded('user', function () {
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                    'email' => $this->user->email,
                ];
            }),
            // OBSERVACIONES
            'observaciones' => $this->observaciones,
            // TIMESTAMPS
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
