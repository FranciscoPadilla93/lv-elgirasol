<?php

namespace App\Http\Requests\School;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
                Rule::exists('cat_parentescos', 'id')
                    ->whereNull('deleted_at')
                    ->where('status', true),
            ],
            'tipo_contacto_id' => [
                'nullable',
                'integer',
                Rule::exists('cat_tipo_contacto', 'id')
                    ->whereNull('deleted_at')
                    ->where('status', true),
            ],
            // DATOS PERSONALES
            'nombre_completo' => [
                'required',
                'string',
                'max:150',
            ],
            // CONTACTO
            'telefono' => [
                'required',
                'string',
                'max:20',
            ],
            'correo' => [
                'required',
                'email',
                'max:255',
            ],
            'uso_obligado' => [
                'required',
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
