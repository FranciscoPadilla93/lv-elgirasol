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
            // ESTADO
            'estado' => $this->whenLoaded('estado', function () {
                return [
                    'id' => $this->estado->id,
                    'code' => $this->estado->code,
                    'name' => $this->estado->name,
                ];
            }),
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

            'foto_url' => $this->foto_path
                ? asset('storage/' . $this->foto_path)
                : null,

            // DOMICILIO
            'domicilio' => [
                'colonia' => $this->colonia,
                'otra_colonia' => $this->otra_colonia,
                'calle' => $this->calle,
                'numero_exterior' => $this->numero_exterior,
                'numero_interior' => $this->numero_interior,
                'codigo_postal' => $this->codigo_postal,
            ],

            // DATOS COMPLEMENTARIOS
            'datos_complementarios' => [
                'procedencia_academica' => $this->procedencia_academica,
                'tipo_escuela' => $this->tipo_escuela,
                'motivo_cambio' => $this->motivo_cambio,
            ],

            // CONSIDERACIONES MÉDICAS
            'datos_medicos' => [
                'alergias' => $this->alergias,
                'alergias_descripcion' => $this->alergias_descripcion,

                'enfermedad_cronica' => $this->enfermedad_cronica,
                'enfermedad_cronica_descripcion' => $this->enfermedad_cronica_descripcion,

                'seguro_medico' => $this->seguro_medico,
                'numero_poliza_seguro' => $this->numero_poliza_seguro,
            ],

            // GRUPO SANGUÍNEO
            'grupo_sanguineo' => $this->whenLoaded(
                'grupoSanguineo',
                function () {

                    return [
                        'id' => $this->grupoSanguineo->id,
                        'code' => $this->grupoSanguineo->code,
                        'name' => $this->grupoSanguineo->name,
                    ];
                }
            ),

            // TIPO SEGURO MÉDICO
            'tipo_seguro_medico' => $this->whenLoaded(
                'tipoSeguroMedico',
                function () {

                    return [
                        'id' => $this->tipoSeguroMedico->id,
                        'code' => $this->tipoSeguroMedico->code,
                        'name' => $this->tipoSeguroMedico->name,
                    ];
                }
            ),

            // RELIGIÓN
            'datos_religiosos' => [
                'religion' => $this->religion,
                'bautizado' => $this->bautizado,
                'primera_comunion' => $this->primera_comunion,
                'confirmado' => $this->confirmado,
            ],

            // TIMESTAMPS
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
