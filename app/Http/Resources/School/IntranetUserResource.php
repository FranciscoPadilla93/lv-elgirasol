<?php

namespace App\Http\Resources\School;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class IntranetUserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            // IDENTIFICACIÓN
            'id' => $this->id,
            // DATOS
            'email' => $this->email,
            'full_name' => $this->full_name,
            'curp' => $this->curp,
            // ESTADO
            'status' => $this->status,
            // AUDITORÍA
            'created_by' => $this->whenLoaded(
                'createdBy',
                fn () => [
                    'id' => $this->createdBy?->id,
                    'name' => $this->createdBy?->name,
                    'email' => $this->createdBy?->email,
                ]
            ),
            'updated_by' => $this->whenLoaded(
                'updatedBy',
                fn () => [
                    'id' => $this->updatedBy?->id,
                    'name' => $this->updatedBy?->name,
                    'email' => $this->updatedBy?->email,
                ]
            ),
            // TIMESTAMPS
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
