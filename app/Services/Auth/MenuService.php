<?php

namespace App\Services\Auth;

use App\Models\Menu;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class MenuService
{
    public function getMenusForUser(User $user): Collection
    {
        $allowedModuleCodes = $this->getAllowedModuleCodes($user);

        return Menu::query()
            ->active()
            ->parents()
            ->with([
                'children' => function ($query) use ($allowedModuleCodes) {
                    $query->active()
                        ->whereHas('module', function ($query) use ($allowedModuleCodes) {
                            $query->whereIn('code', $allowedModuleCodes);
                        })
                        ->with('module')
                        ->orderBy('order');
                },
            ])
            ->orderBy('order')
            ->get()
            ->filter(function ($menu) {
                return $menu->children->isNotEmpty();
            })
            ->values();
    }

    private function getAllowedModuleCodes(User $user): array
    {
        // ROLES CON ACCESO TOTAL
        if ($user->hasRole([
            'super_admin',
            'direccion_general',
        ])) {
            return Menu::query()
                ->whereNotNull('module_id')
                ->whereHas('module')
                ->with('module')
                ->get()
                ->pluck('module.code')
                ->unique()
                ->values()
                ->toArray();
        }

        // USUARIOS NORMALES
        return collect($user->getCachedPermissions())
            ->keys()
            ->filter(function ($key) {
                return str_ends_with($key, ':read');
            })
            ->map(function ($key) {
                return explode(':', $key)[0];
            })
            ->unique()
            ->values()
            ->toArray();
    }
}
