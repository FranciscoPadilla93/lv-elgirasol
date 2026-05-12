<?php

namespace App\Http\Requests\School;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateExpedienteTutorRequest extends FormRequest
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
            'tutor_id' => [
                'sometimes',
                'required',
                'integer',
                'exists:tutores,id',
            ],
            'parentesco_id' => [
                'sometimes',
                'required',
                'integer',
                'exists:cat_parentescos,id',
            ],
            // CONFIGURACIÓN
            'is_primary_contact' => [
                'sometimes',
                'nullable',
                'boolean',
            ],
            'is_financial_responsible' => [
                'sometimes',
                'nullable',
                'boolean',
            ],
            'status' => [
                'sometimes',
                'nullable',
                'boolean',
            ],

            // VALIDACIÓN ÚNICA
            Rule::unique('expediente_tutores')
                ->ignore($this->route('expedienteTutor')->id)
                ->where(function ($query) {
                    return $query
                        ->where(
                            'expediente_id',
                            $this->expediente_id
                        )
                        ->where(
                            'tutor_id',
                            $this->tutor_id
                        )
                        ->where(
                            'parentesco_id',
                            $this->parentesco_id
                        )
                        ->whereNull('deleted_at');
                }),
        ];
    }

    protected function prepareForValidation(): void
    {
        $booleanFields = [
            'is_primary_contact',
            'is_financial_responsible',
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
