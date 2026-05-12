<?php

namespace App\Http\Resources\School;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExpedienteTutorResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            // IDENTIFICACIÓN
            'id' => $this->id,
            // EXPEDIENTE
            'expediente' => $this->whenLoaded(
                'expediente',
                function () {
                    return [
                        'id' => $this->expediente->id,
                        'folio' => $this->expediente->folio,
                        'nombre_completo' => $this->expediente->nombre_completo,
                    ];
                }
            ),
            // TUTOR
            'tutor' => $this->whenLoaded(
                'tutor',
                function () {
                    return [
                        'id' => $this->tutor->id,
                        'nombre_completo' => $this->tutor->nombre_completo,
                        'telefono' => $this->tutor->telefono,
                        'correo' => $this->tutor->correo,
                    ];
                }
            ),
            // PARENTESCO
            'parentesco' => $this->whenLoaded(
                'parentesco',
                function () {
                    return [
                        'id' => $this->parentesco->id,
                        'code' => $this->parentesco->code,
                        'name' => $this->parentesco->name,
                    ];
                }
            ),
            // CONFIGURACIÓN
            'is_primary_contact' => $this->is_primary_contact,
            'is_financial_responsible' => $this->is_financial_responsible,
            'status' => $this->status,
            // TIMESTAMPS
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
