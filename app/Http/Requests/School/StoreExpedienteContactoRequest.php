<?php

namespace App\Http\Requests\School;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreExpedienteContactoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'parentesco_id' => [
                'required',
                'integer',
                'exists:cat_parentescos,id',
            ],
            // DATOS PERSONALES
            'nombre' => [
                'required',
                'string',
                'max:150',
            ],
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

            // CONFIGURACIÓN
            'is_emergency_contact' => [
                'nullable',
                'boolean',
            ],
            'is_authorized_pickup' => [
                'nullable',
                'boolean',
            ],
            'status' => [
                'nullable',
                'boolean',
            ],
            // OBSERVACIONES
            'observaciones' => [
                'nullable',
                'string',
            ],
        ];
    }
}
