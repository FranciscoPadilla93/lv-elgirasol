<?php

namespace App\Http\Requests\School;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateIntranetUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $intranetUser = $this->route('intranetUser');

        $intranetUserId = is_object($intranetUser)
            ? $intranetUser->id
            : $intranetUser;

        return [
            'email' => [
                'sometimes',
                'required',
                'email',
                'max:255',
                Rule::unique('intranet_users', 'email')
                    ->ignore($intranetUserId),
            ],
            'full_name' => [
                'sometimes',
                'required',
                'string',
                'max:1500',
            ],
            'curp' => [
                'sometimes',
                'required',
                'string',
                'size:18',
                Rule::unique('intranet_users', 'curp')
                    ->ignore($intranetUserId),
            ],
            'password' => [
                'sometimes',
                'nullable',
                'string',
                'min:8',
                'max:18',
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
        $data = [];

        if ($this->has('curp')) {
            $data['curp'] = strtoupper($this->curp);
        }

        if ($this->has('email')) {
            $data['email'] = strtolower($this->email);
        }

        if ($this->has('status')) {
            $data['status'] = filter_var(
                $this->status,
                FILTER_VALIDATE_BOOLEAN
            );
        }

        $this->merge($data);
    }
}
