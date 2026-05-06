<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nombre' => $this->name,
            'email' => $this->email,
            'role_id' => $this->role_id,
            'estatus' => $this->status,
            'email_verificado' => $this->email_verified_at !== null,
        ];
    }
}
