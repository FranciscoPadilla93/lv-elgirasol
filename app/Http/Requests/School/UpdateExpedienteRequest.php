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
            // DATOS GENERALES
            'foto' => [
                'sometimes',
                'required',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:15360'
            ],
            'nombre' => ['sometimes','required', 'string', 'max:150'],
            'apellido_paterno' => ['sometimes','required', 'string', 'max:150'],
            'apellido_materno' => ['sometimes','nullable', 'string', 'max:150'],
            'fecha_nacimiento' => ['sometimes','required', 'date'],
            'curp' => [
                'sometimes',
                'required',
                'string',
                'size:18',
                Rule::unique('expedientes', 'curp')
                    ->ignore($this->route('expediente')->id)
                    ->whereNull('deleted_at'),
            ],
            'estado_id' => [
                'sometimes',
                'required',
                'integer',
                Rule::exists('cat_estados', 'id')
                    ->whereNull('deleted_at')
                    ->where('status', true),
            ],
            'genero_id' => [
                'sometimes',
                'nullable',
                'integer',
                Rule::exists('cat_generos', 'id')
                    ->whereNull('deleted_at')
                    ->where('status', true),
            ],
            'fecha_ingreso' => ['sometimes','nullable', 'date'],
            'fecha_baja' => [
                'sometimes',
                'nullable',
                'date',
                'after_or_equal:fecha_ingreso'
            ],
            'motivo_baja' => ['sometimes','nullable', 'string'],
            'observaciones' => ['sometimes','nullable', 'string'],

            // DOMICILIO
            'colonia' => ['sometimes','required', 'string', 'max:500'],
            'otra_colonia' => ['sometimes','nullable', 'string', 'max:500'],
            'calle' => ['sometimes','required', 'string', 'max:500'],
            'numero_exterior' => ['sometimes','required','string','max:20'],
            'numero_interior' => ['sometimes','nullable','string','max:20'],
            'codigo_postal' => ['sometimes','required', 'digits:5'],
            // DATOS COMPLEMENTARIOS
            'procedencia_academica' => ['sometimes','required','string','max:1000'],
            'tipo_escuela' => [
                'sometimes',
                'required',
                Rule::in(['publica', 'privada',
                ])],
            'motivo_cambio' => ['sometimes','nullable','string','max:1500'],
            // CONSIDERACIONES MÉDICAS
            'alergias' => ['sometimes','required','boolean'],
            'alergias_descripcion' => [
                'required_if:alergias,true',
                'nullable',
                'array',
            ],
            'alergias_descripcion.*' => [
                'required',
                'string',
                'max:250',
            ],
            'enfermedad_cronica' => ['sometimes','required','boolean',],
            'enfermedad_cronica_descripcion' => [
                'required_if:enfermedad_cronica,true',
                'nullable',
                'array',
            ],
            'enfermedad_cronica_descripcion.*' => [
                'required',
                'string',
                'max:250',
            ],
            'grupo_sanguineo_id' => [
                'sometimes',
                'required',
                'integer',
                Rule::exists('cat_grupo_sanguineo', 'id')
                    ->whereNull('deleted_at')
                    ->where('status', true),
            ],
            'seguro_medico' => ['sometimes','required','boolean'],
            'tipo_seguro_medico_id' => [
                'nullable',
                'integer',
                'required_if:seguro_medico,true',
                Rule::exists('cat_tipo_seguro_medico', 'id')
                    ->whereNull('deleted_at')
                    ->where('status', true),
            ],
            'numero_poliza_seguro' => [
                'nullable',
                'string',
                'max:20',
                'required_if:seguro_medico,true'
            ],
            // RELIGIÓN
            'religion' => ['sometimes','required','string','max:250'],
            'bautizado' => ['sometimes','required','boolean'],
            'primera_comunion' => ['sometimes','required','boolean'],
            'confirmado' => ['sometimes','required','boolean'],
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
