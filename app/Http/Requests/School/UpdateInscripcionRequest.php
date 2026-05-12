<?php

namespace App\Http\Requests\School;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateInscripcionRequest extends FormRequest
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
                'sometimes',
                'required',
                'integer',
                'exists:expedientes,id',
            ],
            'ciclo_escolar_id' => [
                'sometimes',
                'required',
                'integer',
                'exists:ciclos_escolares,id',
            ],
            'nivel_id' => [
                'sometimes',
                'required',
                'integer',
                'exists:cat_niveles,id',
            ],
            'grado_id' => [
                'sometimes',
                'required',
                'integer',
                'exists:cat_grados,id',
            ],
            // CONFIGURACIÓN
            'is_new_admission' => [
                'sometimes',
                'nullable',
                'boolean',
            ],
            // WORKFLOW
            'estado_inscripcion_id' => [
                'sometimes',
                'required',
                'integer',
                'exists:cat_estados_inscripcion,id',
            ],
            'evaluation_approved' => [
                'sometimes',
                'nullable',
                'boolean',
            ],
            'socioeconomic_study_approved' => [
                'sometimes',
                'nullable',
                'boolean',
            ],
            'treasury_approved' => [
                'sometimes',
                'nullable',
                'boolean',
            ],
            'is_completed' => [
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
            // VALIDACIÓN ÚNICA
            Rule::unique('inscripciones')
                ->ignore($this->route('inscripcion')->id)
                ->where(function ($query) {
                    return $query
                        ->where(
                            'expediente_id',
                            $this->expediente_id
                        )
                        ->where(
                            'ciclo_escolar_id',
                            $this->ciclo_escolar_id
                        )
                        ->whereNull('deleted_at');
                }),
        ];
    }

    protected function prepareForValidation(): void
    {
        $booleanFields = [
            'is_new_admission',
            'evaluation_approved',
            'socioeconomic_study_approved',
            'treasury_approved',
            'is_completed',
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
