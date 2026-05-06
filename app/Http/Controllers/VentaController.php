<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Models\Venta;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class VentaController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $search = $request->string('search')->toString();
        $perPage = $request->integer('per_page', 15);

        $ventas = Venta::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('folio', 'like', "%{$search}%")
                        ->orWhere('cliente', 'like', "%{$search}%")
                        ->orWhere('estatus', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate($perPage);

        return ResponseHelper::success($ventas, 'Ventas obtenidas correctamente.');
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate($this->rules());

        $venta = Venta::create($data);

        return ResponseHelper::success($venta, 'Venta creada correctamente.', 201);
    }

    public function show(Venta $venta): JsonResponse
    {
        return ResponseHelper::success($venta, 'Venta obtenida correctamente.');
    }

    public function update(Request $request, Venta $venta): JsonResponse
    {
        $data = $request->validate($this->rules($venta));

        $venta->update($data);

        return ResponseHelper::success($venta, 'Venta actualizada correctamente.');
    }

    public function destroy(Venta $venta): JsonResponse
    {
        $venta->delete();

        return ResponseHelper::success(null, 'Venta eliminada correctamente.');
    }

    private function rules(?Venta $venta = null): array
    {
        return [
            'folio' => [
                'required',
                'string',
                'max:50',
                Rule::unique('ventas', 'folio')
                    ->ignore($venta?->id)
                    ->whereNull('deleted_at'),
            ],
            'cliente' => ['required', 'string', 'max:255'],
            'fecha_venta' => ['required', 'date'],
            'total' => ['required', 'numeric', 'min:0'],
            'estatus' => ['nullable', 'string', 'max:50'],
            'notas' => ['nullable', 'string'],
        ];
    }
}
