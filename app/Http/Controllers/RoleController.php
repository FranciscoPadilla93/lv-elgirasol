<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $roles = Role::query()
            ->withCount('users')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search');

                $query->where(function ($query) use ($search) {
                    $query->where('code', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->paginate($request->integer('per_page', 15));

        return response()->json([
            'status' => true,
            'status_code' => 200,
            'message' => 'Roles obtenidos correctamente.',
            'data' => $roles,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'code' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z0-9_]+$/',
                Rule::unique('roles', 'code')->whereNull('deleted_at'),
            ],
            'name' => ['required', 'string', 'max:255'],
            'status' => ['nullable', 'string', Rule::in(['active', 'inactive'])],
        ]);

        $role = Role::create([
            'code' => $data['code'],
            'name' => $data['name'],
            'status' => $data['status'] ?? 'active',
        ]);

        return response()->json([
            'status' => true,
            'status_code' => 201,
            'message' => 'Rol creado correctamente.',
            'data' => $role,
        ], 201);
    }

    public function assignToUser(Request $request, User $user): JsonResponse
    {
        $data = $request->validate([
            'role_id' => ['required', 'integer', 'exists:roles,id'],
        ]);

        $role = Role::active()->findOrFail($data['role_id']);

        $user->update([
            'role_id' => $role->id,
        ]);

        return response()->json([
            'status' => true,
            'status_code' => 200,
            'message' => 'Rol asignado correctamente.',
            'data' => $user->load('role'),
        ]);
    }
}
