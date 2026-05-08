<?php

namespace App\Http\Requests\School;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ValidateExpedienteDocumentoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // VALIDACIÓN
            'is_validated' => [
                'required',
                'boolean',
            ],

            'validation_notes' => [
                'nullable',
                'string',
            ],
        ];
    }
}
