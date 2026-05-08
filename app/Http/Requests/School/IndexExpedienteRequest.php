<?php

namespace App\Http\Requests\School;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class IndexExpedienteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // FILTROS
            'search' => ['nullable', 'string', 'max:255'],
            'estado_expediente_id' => ['nullable', 'integer'],
            'genero_id' => ['nullable', 'integer'],
            // PAGINACIÓN
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            // ORDENAMIENTO
            'sort_by' => ['nullable', 'string'],
            'sort_direction' => ['nullable', 'in:asc,desc'],
        ];
    }
}
