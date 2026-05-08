<?php

namespace App\Http\Requests\School;

use Illuminate\Contracts\Validation\ValidationRule;
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
            'apellido_materno' => ['nullable', 'string', 'max:150'],
            'fecha_nacimiento' => ['sometimes', 'required', 'date'],
            'curp' => [
                'nullable',
                'string',
                'size:18',
                Rule::unique('expedientes', 'curp')
                    ->ignore($this->route('expediente')->id),
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
            'fecha_ingreso' => ['nullable', 'date'],
            'fecha_baja' => ['nullable', 'date'],
            'motivo_baja' => ['nullable', 'string'],
            'observaciones' => ['nullable', 'string'],
            // FOTO
            'foto' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],
        ];
    }
}
