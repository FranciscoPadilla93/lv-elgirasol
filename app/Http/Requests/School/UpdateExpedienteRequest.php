<?php

namespace App\Http\Requests\School;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateExpedienteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // DATOS PERSONALES
            'nombre' => ['sometimes', 'required', 'string', 'max:150'],
            'apellido_paterno' => ['sometimes', 'required', 'string', 'max:150'],
            'apellido_materno' => ['sometimes', 'nullable', 'string', 'max:150'],
            'fecha_nacimiento' => ['sometimes', 'required', 'date'],
            'curp' => [
                'sometimes',
                'nullable',
                'string',
                'size:18',
                Rule::unique('expedientes', 'curp')
                    ->ignore($this->route('expediente')->id)
                    ->whereNull('deleted_at'),
            ],
            // RELACIONES
            'genero_id' => [
                'sometimes',
                'required',
                'integer',
                'exists:cat_generos,id',
            ],
            'estado_expediente_id' => [
                'sometimes',
                'required',
                'integer',
                'exists:cat_estados_expediente,id',
            ],

            // INFORMACIÓN GENERAL
            'fecha_ingreso' => ['sometimes', 'nullable', 'date'],
            'fecha_baja' => [
                'sometimes',
                'nullable',
                'date',
                'after_or_equal:fecha_ingreso',
            ],
            'motivo_baja' => ['sometimes', 'nullable', 'string'],
            'observaciones' => ['sometimes', 'nullable', 'string'],
            // FOTO
            'foto' => [
                'sometimes',
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],
            // DOMICILIO
            'colonia' => ['sometimes', 'required', 'string', 'max:500'],
            'otra_colonia' => ['sometimes', 'nullable', 'string', 'max:500'],
            'calle' => ['sometimes', 'required', 'string', 'max:500'],
            'numero_exterior' => ['sometimes', 'required', 'string', 'max:20'],
            'numero_interior' => ['sometimes', 'nullable', 'string', 'max:20'],
            'codigo_postal' => ['sometimes', 'required', 'digits:5'],
            // DATOS COMPLEMENTARIOS
            'procedencia_academica' => ['sometimes', 'nullable', 'string'],
            'tipo_escuela' => [
                'sometimes',
                'nullable',
                Rule::in([
                    'publica',
                    'privada',
                ]),
            ],

            'motivo_cambio' => ['sometimes', 'nullable', 'string'],
            // CONSIDERACIONES MÉDICAS
            'alergias' => ['sometimes', 'nullable', 'boolean'],
            'alergias_descripcion' => [
                'sometimes',
                'nullable',
                'string',
                'max:250',
                'required_if:alergias,true',
            ],
            'enfermedad_cronica' => ['sometimes', 'nullable', 'boolean'],
            'enfermedad_cronica_descripcion' => [
                'sometimes',
                'nullable',
                'string',
                'max:250',
                'required_if:enfermedad_cronica,true',
            ],
            'grupo_sanguineo_id' => [
                'sometimes',
                'nullable',
                'integer',
                'exists:cat_grupo_sanguineo,id',
            ],
            'seguro_medico' => ['sometimes', 'nullable', 'boolean'],
            'tipo_seguro_medico_id' => [
                'sometimes',
                'nullable',
                'integer',
                'exists:cat_tipo_seguro_medico,id',
                'required_if:seguro_medico,true',
            ],
            'numero_poliza_seguro' => [
                'sometimes',
                'nullable',
                'string',
                'max:20',
                'required_if:seguro_medico,true',
            ],
            // RELIGIÓN
            'religion' => ['sometimes', 'nullable', 'string', 'max:250'],
            'bautizado' => ['sometimes', 'nullable', 'boolean'],
            'primera_comunion' => ['sometimes', 'nullable', 'boolean'],
            'confirmado' => ['sometimes', 'nullable', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $booleanFields = [
            'alergias',
            'enfermedad_cronica',
            'seguro_medico',
            'bautizado',
            'primera_comunion',
            'confirmado',
        ];

        $data = [];

        foreach ($booleanFields as $field) {
            if ($this->has($field)) {
                $data[$field] = filter_var($this->$field, FILTER_VALIDATE_BOOLEAN);
            }
        }

        $this->merge($data);
    }
}
