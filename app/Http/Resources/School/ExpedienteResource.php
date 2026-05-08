<?php

namespace App\Http\Resources\School;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExpedienteResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            // IDENTIFICACIÓN
            'id' => $this->id,
            'folio' => $this->folio,
            // DATOS PERSONALES
            'nombre' => $this->nombre,
            'apellido_paterno' => $this->apellido_paterno,
            'apellido_materno' => $this->apellido_materno,
            'nombre_completo' => trim(
                "{$this->nombre} {$this->apellido_paterno} {$this->apellido_materno}"
            ),
            'fecha_nacimiento' => $this->fecha_nacimiento?->format('Y-m-d'),
            'curp' => $this->curp,
            // GÉNERO
            'genero' => $this->whenLoaded('genero', function () {

                return [
                    'id' => $this->genero->id,
                    'code' => $this->genero->code,
                    'name' => $this->genero->name,
                ];
            }),
            // ESTADO EXPEDIENTE
            'estado_expediente' => $this->whenLoaded(
                'estadoExpediente',
                function () {

                    return [
                        'id' => $this->estadoExpediente->id,
                        'code' => $this->estadoExpediente->code,
                        'name' => $this->estadoExpediente->name,
                    ];
                }
            ),
            // FECHAS
            'fecha_ingreso' => $this->fecha_ingreso?->format('Y-m-d'),
            'fecha_baja' => $this->fecha_baja?->format('Y-m-d'),
            // OBSERVACIONES
            'motivo_baja' => $this->motivo_baja,
            'observaciones' => $this->observaciones,
            // FOTO
            'foto_path' => $this->foto_path,
            // TIMESTAMPS
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
