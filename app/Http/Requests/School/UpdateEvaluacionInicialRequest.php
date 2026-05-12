<?php

namespace App\Http\Requests\School;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateEvaluacionInicialRequest extends FormRequest
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
                'sometimes',
                'required',
                'integer',
                'exists:inscripciones,id',
            ],
            'tipo_evaluacion_id' => [
                'sometimes',
                'required',
                'integer',
                'exists:cat_tipos_evaluacion,id',
            ],
            'evaluated_by' => [
                'sometimes',
                'required',
                'integer',
                'exists:users,id',
            ],
            // EVALUACIÓN
            'evaluation_date' => [
                'sometimes',
                'required',
                'date',
            ],
            'score' => [
                'sometimes',
                'required',
                'numeric',
                'min:0',
                'max:100',
            ],
            // APROBACIÓN
            'is_approved' => [
                'sometimes',
                'nullable',
                'boolean',
            ],
            // OBSERVACIONES
            'observaciones' => [
                'sometimes',
                'nullable',
                'string',
            ],
            // STATUS
            'status' => [
                'sometimes',
                'nullable',
                'boolean',
            ],
        ];
    }

    protected function prepareForValidation(): void
    {
        $booleanFields = [
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
