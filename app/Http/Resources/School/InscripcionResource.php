<?php

namespace App\Http\Resources\School;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InscripcionResource extends JsonResource
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
            // CICLO ESCOLAR
            'ciclo_escolar' => $this->whenLoaded(
                'cicloEscolar',
                function () {
                    return [
                        'id' => $this->cicloEscolar->id,
                        'code' => $this->cicloEscolar->code,
                        'name' => $this->cicloEscolar->name,
                    ];
                }
            ),
            // NIVEL
            'nivel' => $this->whenLoaded(
                'nivel',
                function () {
                    return [
                        'id' => $this->nivel->id,
                        'code' => $this->nivel->code,
                        'name' => $this->nivel->name,
                    ];
                }
            ),
            // GRADO
            'grado' => $this->whenLoaded(
                'grado',
                function () {
                    return [
                        'id' => $this->grado->id,
                        'code' => $this->grado->code,
                        'name' => $this->grado->name,
                    ];
                }
            ),
            // ESTADO
            'estado_inscripcion' => $this->whenLoaded(
                'estadoInscripcion',
                function () {
                    return [
                        'id' => $this->estadoInscripcion->id,
                        'code' => $this->estadoInscripcion->code,
                        'name' => $this->estadoInscripcion->name,
                    ];
                }
            ),
            // CONFIGURACIÓN
            'is_new_admission' => $this->is_new_admission,
            'inscription_date' => $this->inscription_date?->toISOString(),
            // REQUISITOS
            'requires_evaluation' => $this->requires_evaluation,
            'requires_socioeconomic_study' => $this->requires_socioeconomic_study,
            'requires_treasury_validation' => $this->requires_treasury_validation,
            // APROBACIONES
            'evaluation_approved' => $this->evaluation_approved,
            'socioeconomic_study_approved' => $this->socioeconomic_study_approved,
            'treasury_approved' => $this->treasury_approved,
            // COMPLETADO
            'is_completed' => $this->is_completed,
            // OBSERVACIONES
            'observaciones' => $this->observaciones,
            // TIMESTAMPS
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
