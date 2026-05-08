<?php

namespace App\Http\Requests\School;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreExpedienteDocumentoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // RELACIONES
            'expediente_id' => [
                'required',
                'integer',
                'exists:expedientes,id',
            ],
            'tipo_documento_id' => [
                'required',
                'integer',
                'exists:cat_tipos_documento,id',
            ],
            // ARCHIVO
            'archivo' => [
                'required',
                'file',
                'max:10240',
            ],
            // VALIDACIÓN
            'validation_notes' => [
                'nullable',
                'string',
            ],
        ];
    }
}
