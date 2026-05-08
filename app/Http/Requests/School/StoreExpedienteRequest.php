<?php

namespace App\Http\Requests\School;

use Illuminate\Contracts\Validation\ValidationRule;
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
            'curp' => ['nullable', 'string', 'size:18', 'unique:expedientes,curp'],
            // RELACIONES
            'genero_id' => [
                'required',
                'integer',
                'exists:cat_generos,id',
            ],
            'estado_expediente_id' => [
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
