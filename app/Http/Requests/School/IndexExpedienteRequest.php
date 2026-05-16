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
            'search' => [
                'nullable',
                'string',
                'max:100',
            ],
            'per_page' => [
                'nullable',
                'integer',
                'min:1',
                'max:100',
            ],
            'sort_by' => [
                'nullable',
                'string',
                'in:id,folio,nombre,apellido_paterno,apellido_materno,curp,created_at',
            ],
            'sort_direction' => [
                'nullable',
                'string',
                'in:asc,desc',
            ],

            // FILTROS
            'estado_id' => [
                'nullable',
                'integer',
                'exists:cat_estados,id',
            ],
            'estado_expediente_id' => [
                'nullable',
                'integer',
                'exists:cat_estados_expediente,id',
            ],
            'genero_id' => [
                'nullable',
                'integer',
                'exists:cat_generos,id',
            ],
            'grupo_sanguineo_id' => [
                'nullable',
                'integer',
                'exists:cat_grupos_sanguineos,id',
            ],
            'tipo_seguro_medico_id' => [
                'nullable',
                'integer',
                'exists:cat_tipos_seguro_medico,id',
            ],
        ];
    }
}
