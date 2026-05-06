<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UsuarioResource extends JsonResource
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
            'rol' => [
                'id' => $this->role?->id,
                'codigo' => $this->role?->code,
                'nombre' => $this->role?->name,
                'estatus' => $this->role?->status,
            ],
            'permisos' => array_keys($this->getCachedPermissions()),
            'permisos_detallados' => $this->permissionsByModule(),
        ];
    }

    protected function permissionsByModule(): array
    {
        return $this->role?->rolePermissions
            ->where('allowed', true)
            ->map(function ($rolePermission) {
                return [
                    'modulo' => [
                        'id' => $rolePermission->module?->id,
                        'codigo' => $rolePermission->module?->code,
                        'nombre' => $rolePermission->module?->name,
                        'estatus' => $rolePermission->module?->status,
                    ],
                    'permiso' => [
                        'id' => $rolePermission->permission?->id,
                        'codigo' => $rolePermission->permission?->code,
                        'nombre' => $rolePermission->permission?->name,
                    ],
                ];
            })
            ->groupBy('modulo.codigo')
            ->map(function ($items) {
                $first = $items->first();

                return [
                    'modulo' => $first['modulo'],
                    'permisos' => $items
                        ->pluck('permiso')
                        ->values()
                        ->all(),
                ];
            })
            ->values()
            ->all() ?? [];
    }
}
