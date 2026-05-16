<?php

namespace App\Http\Resources\School;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExpedienteDocumentoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'expediente_id' => $this->expediente_id,
            'tipo_documento_id' => $this->tipo_documento_id,
            'tipo_documento' => $this->whenLoaded('tipoDocumento', function () {
                return [
                    'id' => $this->tipoDocumento?->id,
                    'nombre' => $this->tipoDocumento?->name,
                    'clave' => $this->tipoDocumento?->code ?? null,
                    'status' => $this->tipoDocumento?->status ?? null,
                ];
            }),
            'archivo' => [
                'original_name' => $this->original_name,
                'file_name' => $this->file_name,
                'file_path' => $this->file_path,
                'mime_type' => $this->mime_type,
                'extension' => $this->extension,
                'size_bytes' => $this->size_bytes,
                'human_size' => $this->human_size,
            ],
            'validacion' => [
                'is_validated' => $this->is_validated,
                'validated_by' => $this->validated_by,
                'validated_at' => $this->validated_at?->format('Y-m-d H:i:s'),
                'validation_notes' => $this->validation_notes,
                'validated_by_user' => $this->whenLoaded('validatedBy', function () {
                    return [
                        'id' => $this->validatedBy?->id,
                        'name' => $this->validatedBy?->name,
                        'email' => $this->validatedBy?->email,
                    ];
                }),
            ],
            'status' => $this->status,
            'auditoria' => [
                'created_by' => $this->created_by,
                'updated_by' => $this->updated_by,

                'created_by_user' => $this->whenLoaded('createdBy', function () {
                    return [
                        'id' => $this->createdBy?->id,
                        'name' => $this->createdBy?->name,
                    ];
                }),
                'updated_by_user' => $this->whenLoaded('updatedBy', function () {
                    return [
                        'id' => $this->updatedBy?->id,
                        'name' => $this->updatedBy?->name,
                    ];
                }),
            ],
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            'deleted_at' => $this->deleted_at?->format('Y-m-d H:i:s'),
        ];
    }
}
