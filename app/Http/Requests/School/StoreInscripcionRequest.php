<?php

namespace App\Http\Requests\School;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class StoreInscripcionRequest extends FormRequest
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
            'ciclo_escolar_id' => [
                'required',
                'integer',
                'exists:ciclos_escolares,id',
            ],
            'nivel_id' => [
                'required',
                'integer',
                'exists:cat_niveles,id',
            ],
            'grado_id' => [
                'required',
                'integer',
                'exists:cat_grados,id',
            ],
            // CONFIGURACIÓN
            'is_new_admission' => [
                'nullable',
                'boolean',
            ],
            // OBSERVACIONES
            'observaciones' => [
                'nullable',
                'string',
            ],

            // VALIDACIÓN ÚNICA
            Rule::unique('inscripciones')
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
        $booleanFields = ['is_new_admission',];
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
