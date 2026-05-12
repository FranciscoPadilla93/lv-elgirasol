<?php

namespace App\Http\Requests\School;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTutorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
             // DATOS PERSONALES
            'nombre' => [
                'sometimes',
                'required',
                'string',
                'max:150',
            ],
            'apellido_paterno' => [
                'sometimes',
                'required',
                'string',
                'max:150',
            ],
            'apellido_materno' => [
                'sometimes',
                'nullable',
                'string',
                'max:150',
            ],
            'curp' => [
                'sometimes',
                'nullable',
                'string',
                'size:18',
                Rule::unique('tutores', 'curp')
                    ->ignore($this->route('tutor')->id)
                    ->whereNull('deleted_at'),
            ],
            'genero_id' => [
                'sometimes',
                'required',
                'integer',
                'exists:cat_generos,id',
            ],
            // CONTACTO
            'telefono' => [
                'sometimes',
                'required',
                'string',
                'max:20',
            ],
            'telefono_secundario' => [
                'sometimes',
                'nullable',
                'string',
                'max:20',
            ],
            'correo' => [
                'sometimes',
                'nullable',
                'email',
                'max:255',
            ],
            // INFORMACIÓN LABORAL
            'empresa' => [
                'sometimes',
                'nullable',
                'string',
                'max:255',
            ],
            'puesto' => [
                'sometimes',
                'nullable',
                'string',
                'max:255',
            ],
            // INTRANET
            'user_id' => [
                'sometimes',
                'nullable',
                'integer',
                'exists:users,id',
            ],
            // OBSERVACIONES
            'observaciones' => [
                'sometimes',
                'nullable',
                'string',
            ],
        ];
    }
}
