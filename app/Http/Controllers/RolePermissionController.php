<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\RolePermission;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RolePermissionController extends Controller
{
    public function sync(Request $request, Role $role): JsonResponse
    {
        $data = $request->validate([
            'permissions' => ['required', 'array'],
            'permissions.*.module_id' => ['required', 'integer', 'exists:modules,id'],
            'permissions.*.permission_id' => ['required', 'integer', 'exists:permissions,id'],
            'permissions.*.allowed' => ['sometimes', 'boolean'],
        ]);

        DB::transaction(function () use ($data, $role) {
            $role->rolePermissions()->get()->each->delete();

            foreach ($data['permissions'] as $permission) {
                RolePermission::create([
                    'role_id' => $role->id,
                    'module_id' => $permission['module_id'],
                    'permission_id' => $permission['permission_id'],
                    'allowed' => $permission['allowed'] ?? true,
                ]);
            }
        });

        User::flushRolePermissionsCache($role->id);

        return response()->json([
            'status' => true,
            'status_code' => 200,
            'message' => 'Permisos del rol sincronizados correctamente.',
            'data' => $role->load(['rolePermissions.module', 'rolePermissions.permission']),
        ]);
    }
}
