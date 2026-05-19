<?php

namespace App\Http\Resources\School;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ConceptoCicloEscolarResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'concepto_id' => $this->concepto_id,
            'concepto' => $this->whenLoaded('concepto', function () {
                return [
                    'id' => $this->concepto->id,
                    'code' => $this->concepto->code,
                    'name' => $this->concepto->name,
                ];
            }),
            'ciclo_escolar_id' => $this->ciclo_escolar_id,
            'ciclo_escolar' => $this->whenLoaded('cicloEscolar', function () {
                return [
                    'id' => $this->cicloEscolar->id,
                    'code' => $this->cicloEscolar->code,
                    'name' => $this->cicloEscolar->name,
                    'is_current' => (bool) $this->cicloEscolar->is_current,
                ];
            }),
            'price' => $this->price,
            'start_date' => $this->start_date?->format('Y-m-d'),
            'due_date' => $this->due_date?->format('Y-m-d'),
            'has_late_fee' => (bool) $this->has_late_fee,
            'late_fee_percentage' => $this->late_fee_percentage,
            'status' => (bool) $this->status,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            'deleted_at' => $this->deleted_at?->format('Y-m-d H:i:s'),
        ];;
    }
}
