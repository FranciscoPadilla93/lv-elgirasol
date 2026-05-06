<?php

namespace App\Http\Controllers;

use App\Models\Module;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ModuleController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'code' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z0-9_]+$/',
                Rule::unique('modules', 'code')->whereNull('deleted_at'),
            ],
            'name' => ['required', 'string', 'max:255'],
            'status' => ['nullable', 'string', Rule::in(['active', 'inactive'])],
        ]);

        $module = Module::create([
            'code' => $data['code'],
            'name' => $data['name'],
            'status' => $data['status'] ?? 'active',
        ]);

        return response()->json([
            'status' => true,
            'status_code' => 201,
            'message' => 'Modulo creado correctamente.',
            'data' => $module,
        ], 201);
    }
}
