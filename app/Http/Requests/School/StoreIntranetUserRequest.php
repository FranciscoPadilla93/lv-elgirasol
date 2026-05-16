<?php

namespace App\Http\Requests\School;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreIntranetUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => [
                'required',
                'email',
                'max:255',
                'unique:intranet_users,email',
            ],
            'full_name' => [
                'required',
                'string',
                'max:1500',
            ],
            'curp' => [
                'required',
                'string',
                'size:18',
                'unique:intranet_users,curp',
            ],
            'password' => [
                'required',
                'string',
                'min:8',
                'max:18',
            ],
            'status' => [
                'nullable',
                'boolean',
            ],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('curp')) {
            $this->merge([
                'curp' => strtoupper($this->curp),
            ]);
        }

        if ($this->has('email')) {
            $this->merge([
                'email' => strtolower($this->email),
            ]);
        }

        if ($this->has('status')) {
            $this->merge([
                'status' => filter_var(
                    $this->status,
                    FILTER_VALIDATE_BOOLEAN
                ),
            ]);
        }
    }
}
