<?php

namespace App\Http\Requests\School;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCicloEscolarRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $cicloEscolar = $this->route('cicloEscolar');

        $cicloEscolarId = is_object($cicloEscolar)
            ? $cicloEscolar->id
            : $cicloEscolar;

        return [
            'code' => [
                'sometimes',
                'required',
                'string',
                'max:50',
                Rule::unique('ciclos_escolares', 'code')
                    ->ignore($cicloEscolarId),
            ],
            'name' => [
                'sometimes',
                'required',
                'string',
                'max:100',
            ],
            'start_date' => [
                'sometimes',
                'required',
                'date',
            ],
            'end_date' => [
                'sometimes',
                'required',
                'date',
                'after:start_date',
            ],
            'is_current' => [
                'sometimes',
                'nullable',
                'boolean',
            ],
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
            'is_current',
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
