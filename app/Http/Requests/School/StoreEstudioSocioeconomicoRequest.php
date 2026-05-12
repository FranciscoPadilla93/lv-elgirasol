<?php

namespace App\Http\Requests\School;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreEstudioSocioeconomicoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // RELACIONES
            'inscripcion_id' => [
                'required',
                'integer',
                'exists:inscripciones,id',
            ],
            // ENVÍO
            'submitted_by_tutor' => [
                'nullable',
                'boolean',
            ],
            // RESPUESTAS
            'responses' => [
                'nullable',
                'array',
            ],
            // APROBACIÓN
            'is_approved' => [
                'nullable',
                'boolean',
            ],
            'approval_notes' => [
                'nullable',
                'string',
            ],
            // STATUS
            'status' => [
                'nullable',
                'boolean',
            ],
        ];
    }

    protected function prepareForValidation(): void
    {
        $booleanFields = [
            'submitted_by_tutor',
            'is_approved',
            'status',
        ];

        $data = [];

        foreach ($booleanFields as $field) {
            if ($this->has($field)) {
                $data[$field] = filter_var(
                    $this->$field,
                    FILTER_VALIDATE_BOOLEAN
                );
            }
        }

        $this->merge($data);
    }
}
