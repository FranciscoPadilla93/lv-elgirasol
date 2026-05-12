<?php

namespace App\Http\Requests\School;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreEvaluacionInicialRequest extends FormRequest
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
            'tipo_evaluacion_id' => [
                'required',
                'integer',
                'exists:cat_tipos_evaluacion,id',
            ],
            'evaluated_by' => [
                'required',
                'integer',
                'exists:users,id',
            ],
            // EVALUACIÓN
            'evaluation_date' => [
                'required',
                'date',
            ],
            'score' => [
                'required',
                'numeric',
                'min:0',
                'max:100',
            ],
            // OBSERVACIONES
            'observaciones' => [
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
        $booleanFields = ['status'];
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
