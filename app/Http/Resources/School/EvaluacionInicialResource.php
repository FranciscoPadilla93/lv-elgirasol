<?php

namespace App\Http\Resources\School;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EvaluacionInicialResource extends JsonResource
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
            // TIPO EVALUACIÓN
            'tipo_evaluacion' => $this->whenLoaded(
                'tipoEvaluacion',
                function () {
                    return [
                        'id' => $this->tipoEvaluacion->id,
                        'code' => $this->tipoEvaluacion->code,
                        'name' => $this->tipoEvaluacion->name,
                    ];
                }
            ),
            // EVALUADOR
            'evaluator' => $this->whenLoaded(
                'evaluator',
                function () {
                    return [
                        'id' => $this->evaluator->id,
                        'name' => $this->evaluator->name,
                        'email' => $this->evaluator->email,
                    ];
                }
            ),
            // EVALUACIÓN
            'attempt' => $this->attempt,
            'evaluation_date' => $this->evaluation_date?->format('Y-m-d'),
            'score' => $this->score,
            'is_approved' => $this->is_approved,
            // OBSERVACIONES
            'observaciones' => $this->observaciones,
            // STATUS
            'status' => $this->status,
            // TIMESTAMPS
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
