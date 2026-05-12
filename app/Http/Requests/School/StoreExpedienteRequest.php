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
            // DATOS PERSONALES
            'nombre' => ['required', 'string', 'max:150'],
            'apellido_paterno' => ['required', 'string', 'max:150'],
            'apellido_materno' => ['nullable', 'string', 'max:150'],
            'fecha_nacimiento' => ['required', 'date'],
            'curp' => [
                'nullable',
                'string',
                'size:18',
                Rule::unique('expedientes', 'curp')->whereNull('deleted_at'),
            ],
            // RELACIONES
            'genero_id' => [
                'required',
                'integer',
                'exists:cat_generos,id'
            ],
            'estado_expediente_id' => [
                'required',
                'integer',
                'exists:cat_estados_expediente,id'
            ],
            // INFORMACIÓN GENERAL
            'fecha_ingreso' => ['nullable', 'date'],
            'fecha_baja' => ['nullable', 'date', 'after_or_equal:fecha_ingreso'],
            'motivo_baja' => ['nullable', 'string'],
            'observaciones' => ['nullable', 'string'],
            // FOTO
            'foto' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120'
            ],
            // DOMICILIO
            'colonia' => ['required', 'string', 'max:500'],
            'otra_colonia' => ['nullable', 'string', 'max:500'],
            'calle' => ['required', 'string', 'max:500'],
            'numero_exterior' => ['required','string','max:20'],
            'numero_interior' => ['nullable','string','max:20'],
            'codigo_postal' => ['required', 'digits:5'],
            // DATOS COMPLEMENTARIOS
            'procedencia_academica' => ['nullable','string'],
            'tipo_escuela' => [
                'nullable',
                Rule::in([
                    'publica',
                    'privada',
                ])],
            'motivo_cambio' => ['nullable','string'],
            // CONSIDERACIONES MÉDICAS
            'alergias' => ['nullable','boolean'],
            'alergias_descripcion' => ['required_if:alergias,true', 'nullable', 'string', 'max:250'],
            'enfermedad_cronica' => ['nullable','boolean',],
            'enfermedad_cronica_descripcion' => ['nullable','string','max:250','required_if:enfermedad_cronica,true'],
            'grupo_sanguineo_id' => ['nullable','integer','exists:cat_grupo_sanguineo,id'],
            'seguro_medico' => ['nullable','boolean'],
            'tipo_seguro_medico_id' => ['nullable','integer','exists:cat_tipo_seguro_medico,id','required_if:seguro_medico,true'],
            'numero_poliza_seguro' => ['nullable','string','max:20','required_if:seguro_medico,true'],
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
