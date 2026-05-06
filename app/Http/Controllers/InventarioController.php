<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Models\Inventario;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class InventarioController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $search = $request->string('search')->toString();
        $perPage = $request->integer('per_page', 15);

        $inventarios = Inventario::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('sku', 'like', "%{$search}%")
                        ->orWhere('producto', 'like', "%{$search}%")
                        ->orWhere('ubicacion', 'like', "%{$search}%")
                        ->orWhere('estatus', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate($perPage);

        return ResponseHelper::success($inventarios, 'Inventario obtenido correctamente.');
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate($this->rules());

        $inventario = Inventario::create($data);

        return ResponseHelper::success($inventario, 'Producto de inventario creado correctamente.', 201);
    }

    public function show(Inventario $inventario): JsonResponse
    {
        return ResponseHelper::success($inventario, 'Producto de inventario obtenido correctamente.');
    }

    public function update(Request $request, Inventario $inventario): JsonResponse
    {
        $data = $request->validate($this->rules($inventario));

        $inventario->update($data);

        return ResponseHelper::success($inventario, 'Producto de inventario actualizado correctamente.');
    }

    public function destroy(Inventario $inventario): JsonResponse
    {
        $inventario->delete();

        return ResponseHelper::success(null, 'Producto de inventario eliminado correctamente.');
    }

    private function rules(?Inventario $inventario = null): array
    {
        return [
            'sku' => [
                'required',
                'string',
                'max:50',
                Rule::unique('inventarios', 'sku')
                    ->ignore($inventario?->id)
                    ->whereNull('deleted_at'),
            ],
            'producto' => ['required', 'string', 'max:255'],
            'cantidad' => ['required', 'integer', 'min:0'],
            'precio_unitario' => ['required', 'numeric', 'min:0'],
            'ubicacion' => ['nullable', 'string', 'max:255'],
            'estatus' => ['nullable', 'string', 'max:50'],
        ];
    }
}
