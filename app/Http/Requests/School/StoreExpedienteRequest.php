<?php

namespace App\Http\Requests\School;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreExpedienteRequest extends FormRequest
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
                'required',
                'image',
                'mimes:jpg,jpeg,png',
                'max:10240'
            ],
            'nombre' => ['required', 'string', 'max:150'],
            'apellido_paterno' => ['required', 'string', 'max:150'],
            'apellido_materno' => ['nullable', 'string', 'max:150'],
            'fecha_nacimiento' => ['required', 'date'],
            'curp' => [
                'required',
                'string',
                'size:18',
                Rule::unique('expedientes', 'curp')->whereNull('deleted_at'),
            ],
            'estado_id' => [
                'required',
                'integer',
                Rule::exists('cat_estados', 'id')->whereNull('deleted_at')->where('status', true),
            ],
            'genero_id' => [
                'nullable',
                'integer',
                Rule::exists('cat_generos', 'id')->whereNull('deleted_at')->where('status', true),
            ],
            'fecha_ingreso' => ['nullable', 'date'],
            'fecha_baja' => ['nullable', 'date', 'after_or_equal:fecha_ingreso'],
            'motivo_baja' => ['nullable', 'string'],
            'observaciones' => ['nullable', 'string'],

            // DOMICILIO
            'colonia' => ['nullable', 'string', 'max:500'],
            'otra_colonia' => ['nullable', 'string', 'max:500'],
            'calle' => ['nullable', 'string', 'max:500'],
            'numero_exterior' => ['nullable','string','max:20'],
            'numero_interior' => ['nullable','string','max:20'],
            'codigo_postal' => ['nullable', 'digits:5'],
            // DATOS COMPLEMENTARIOS
            'procedencia_academica' => ['nullable','string','max:1000'],
            'tipo_escuela' => [
                'nullable',
                Rule::in(['publica', 'privada',
                ])],
            'motivo_cambio' => ['nullable','string','max:1500'],
            // CONSIDERACIONES MÉDICAS
            'alergias' => ['nullable','boolean'],
            'alergias_descripcion' => [
                'required_if:alergias,true',
                'nullable',
                'array'
            ],
            'alergias_descripcion.*' => [
                'required',
                'string',
                'max:250',
            ],
            'enfermedad_cronica' => ['nullable','boolean',],
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
                'nullable',
                'integer',
                Rule::exists('cat_grupo_sanguineo', 'id')
                    ->whereNull('deleted_at')
                    ->where('status', true),
            ],
            'seguro_medico' => ['nullable','boolean'],
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
            'religion' => ['nullable','string','max:250'],
            'bautizado' => ['nullable','boolean'],
            'primera_comunion' => ['nullable','boolean'],
            'confirmado' => ['nullable','boolean'],
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
