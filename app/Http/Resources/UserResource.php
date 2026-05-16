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
            'apellido_paterno' => $this->apellido_paterno,
            'apellido_materno' => $this->apellido_materno,
            'email' => $this->email,
            'puesto' => $this->puesto,
            'cedula_profesional' => $this->cedula_profesional,
            'role_id' => $this->role_id,
            'estatus' => $this->status,
            'email_verificado' => $this->email_verified_at !== null,
        ];
    }
}
