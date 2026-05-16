<?php

namespace App\Http\Requests\School;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateConceptoCicloEscolarRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $conceptoCicloEscolar = $this->route('conceptoCicloEscolar');

        $conceptoCicloEscolarId = is_object($conceptoCicloEscolar)
            ? $conceptoCicloEscolar->id
            : $conceptoCicloEscolar;

        $cicloEscolarId = $this->input(
            'ciclo_escolar_id',
            is_object($conceptoCicloEscolar)
                ? $conceptoCicloEscolar->ciclo_escolar_id
                : null
        );

        return [
            'concepto_id' => [
                'sometimes',
                'required',
                'integer',
                'exists:conceptos,id',
                Rule::unique('conceptos_ciclos_escolares')
                    ->where(function ($query) use ($cicloEscolarId) {
                        return $query
                            ->where('ciclo_escolar_id', $cicloEscolarId)
                            ->whereNull('deleted_at');
                    })
                    ->ignore($conceptoCicloEscolarId),
            ],
            'ciclo_escolar_id' => [
                'sometimes',
                'required',
                'integer',
                'exists:ciclos_escolares,id',
            ],
            'price' => [
                'sometimes',
                'required',
                'numeric',
                'min:0',
                'max:999999.99',
            ],
            'start_date' => [
                'nullable',
                'date',
            ],
            'due_date' => [
                'nullable',
                'date',
                'after_or_equal:start_date',
            ],
            'has_late_fee' => [
                'nullable',
                'boolean',
            ],
            'status' => [
                'nullable',
                'boolean',
            ],
        ];
    }

    protected function prepareForValidation(): void
    {
        $booleanFields = [
            'has_late_fee',
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
