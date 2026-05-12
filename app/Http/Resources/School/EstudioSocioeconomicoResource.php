<?php

namespace App\Http\Resources\School;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EstudioSocioeconomicoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            // IDENTIFICACIÓN
            'id' => $this->id,
            // INSCRIPCIÓN
            'inscripcion' => $this->whenLoaded(
                'inscripcion',
                function () {
                    return [
                        'id' => $this->inscripcion->id,
                        'expediente' => [
                            'id' => $this->inscripcion->expediente?->id,
                            'folio' => $this->inscripcion->expediente?->folio,
                            'nombre_completo' => $this->inscripcion->expediente?->nombre_completo,
                        ],
                    ];
                }
            ),
            // APROBADOR
            'approver' => $this->whenLoaded(
                'approver',
                function () {
                    return [
                        'id' => $this->approver->id,
                        'name' => $this->approver->name,
                        'email' => $this->approver->email,
                    ];
                }
            ),
            // ENVÍO
            'submitted_by_tutor' => $this->submitted_by_tutor,
            'submitted_at' => $this->submitted_at?->toISOString(),
            // RESPUESTAS
            'responses' => $this->responses,
            // APROBACIÓN
            'is_approved' => $this->is_approved,
            'approved_at' => $this->approved_at?->toISOString(),
            'approval_notes' => $this->approval_notes,
            // STATUS
            'status' => $this->status,
            // TIMESTAMPS
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
