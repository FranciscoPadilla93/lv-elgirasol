<?php

namespace App\Http\Requests\School;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateExpedienteContactoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'parentesco_id' => [
                'sometimes',
                'nullable',
                'integer',
                'exists:cat_parentescos,id',
            ],
            // DATOS PERSONALES
            'nombre' => [
                'sometimes',
                'nullable',
                'string',
                'max:150',
            ],
            'apellido_paterno' => [
                'sometimes',
                'nullable',
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
                'sometimes',
                'nullable',
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

    protected function prepareForValidation(): void
    {
        $booleanFields = [
            'is_emergency_contact',
            'is_authorized_pickup',
            'status',
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
