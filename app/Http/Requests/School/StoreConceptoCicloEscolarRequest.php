<?php

namespace App\Http\Requests\School;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class StoreConceptoCicloEscolarRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'concepto_id' => [
                'required',
                'integer',
                'exists:conceptos,id',
                Rule::unique('conceptos_ciclos_escolares')
                    ->where(function ($query) {
                        return $query
                            ->where('ciclo_escolar_id', $this->ciclo_escolar_id)
                            ->whereNull('deleted_at');
                    }),
            ],
            'ciclo_escolar_id' => [
                'required',
                'integer',
                'exists:ciclos_escolares,id',
            ],
            'price' => [
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
            'late_fee_percentage' => [
                'nullable',
                'required_if:has_late_fee,true',
                'numeric',
                'min:0',
                'max:100',
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
