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
                Rule::exists('cat_parentescos', 'id')
                    ->whereNull('deleted_at')
                    ->where('status', true),
            ],
            'tipo_contacto_id' => [
                'sometimes',
                'nullable',
                'integer',
                Rule::exists('cat_tipo_contacto', 'id')
                    ->whereNull('deleted_at')
                    ->where('status', true),
            ],
            // DATOS PERSONALES
            'nombre_completo' => [
                'sometimes',
                'required',
                'string',
                'max:150',
            ],
            // CONTACTO
            'telefono' => [
                'sometimes',
                'required',
                'string',
                'max:20',
            ],
            'correo' => [
                'sometimes',
                'required',
                'email',
                'max:255',
            ],
            'uso_obligado' => [
                'sometimes',
                'nullable',
                'boolean',
            ],
        ];
    }

    protected function prepareForValidation(): void
    {
        $booleanFields = [
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
