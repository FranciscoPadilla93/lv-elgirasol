<?php

namespace App\Http\Requests\School;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTutorRequest extends FormRequest
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

            'apellido_paterno' => [
                'required',
                'string',
                'max:150',
            ],
            'apellido_materno' => [
                'nullable',
                'string',
                'max:150',
            ],
            'curp' => [
                'nullable',
                'string',
                'size:18',
                Rule::unique('tutores', 'curp')
                    ->whereNull('deleted_at'),
            ],
            'genero_id' => [
                'required',
                'integer',
                'exists:cat_generos,id',
            ],
            // CONTACTO
            'telefono' => [
                'required',
                'string',
                'max:20',
            ],
            'telefono_secundario' => [
                'nullable',
                'string',
                'max:20',
            ],
            'correo' => [
                'nullable',
                'email',
                'max:255',
            ],
            // INFORMACIÓN LABORAL
            'empresa' => [
                'nullable',
                'string',
                'max:255',
            ],
            'puesto' => [
                'nullable',
                'string',
                'max:255',
            ],
            // INTRANET
            'user_id' => [
                'nullable',
                'integer',
                'exists:users,id',
            ],
            // OBSERVACIONES
            'observaciones' => [
                'nullable',
                'string',
            ],
        ];
    }
}
