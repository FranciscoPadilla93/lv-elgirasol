<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Helpers\ResponseHelper;
use App\Http\Requests\School\StoreCicloEscolarRequest;
use App\Http\Requests\School\UpdateCicloEscolarRequest;
use App\Http\Resources\School\CicloEscolarResource;
use App\Models\Catalogs\CicloEscolar;
use App\Services\School\CicloEscolarService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CicloEscolarController extends Controller
{
    public function __construct(
        private CicloEscolarService $cicloEscolarService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $search = $request->string('search')->toString();
        $perPage = $request->integer('per_page', 15);
        $sortBy = $request->string('sort_by')->toString() ?: 'id';
        $sortDirection = $request->string('sort_direction')->toString() ?: 'desc';

        $ciclos = CicloEscolar::query()
            // SEARCH
            ->when($search !== '', function ($query) use ($search) {
                $query->where('code', 'LIKE', "%{$search}%")
                    ->orWhere('name', 'LIKE', "%{$search}%");
            })
            // FILTRO ACTUAL
            ->when(
                $request->filled('is_current'),
                function ($query) use ($request) {
                    $query->where(
                        'is_current',
                        filter_var(
                            $request->is_current,
                            FILTER_VALIDATE_BOOLEAN
                        )
                    );
                }
            )
            // FILTRO STATUS
            ->when(
                $request->filled('status'),
                function ($query) use ($request) {
                    $query->where(
                        'status',
                        filter_var(
                            $request->status,
                            FILTER_VALIDATE_BOOLEAN
                        )
                    );
                }
            )
            // ORDENAMIENTO
            ->orderBy($sortBy, $sortDirection)
            // PAGINACIÓN
            ->paginate($perPage);

        return ResponseHelper::success(
            CicloEscolarResource::collection($ciclos),
            'Ciclos escolares obtenidos correctamente.'
        );
    }

    public function show(CicloEscolar $cicloEscolar): JsonResponse {
        return ResponseHelper::success(
            new CicloEscolarResource($cicloEscolar),
            'Ciclo escolar obtenido correctamente.'
        );
    }

    public function store(StoreCicloEscolarRequest $request): JsonResponse {
        $cicloEscolar = $this->cicloEscolarService->create($request->validated());

        return ResponseHelper::success(
            new CicloEscolarResource($cicloEscolar),
            'Ciclo escolar creado correctamente.',
            201
        );
    }

    public function update(UpdateCicloEscolarRequest $request, CicloEscolar $cicloEscolar): JsonResponse {
        $cicloEscolar = $this->cicloEscolarService
            ->update(
                $cicloEscolar,
                $request->validated()
            );

        return ResponseHelper::success(
            new CicloEscolarResource($cicloEscolar),
            'Ciclo escolar actualizado correctamente.'
        );
    }

    public function destroy(CicloEscolar $cicloEscolar): JsonResponse {
        $this->cicloEscolarService->delete($cicloEscolar);

        return ResponseHelper::success(
            null,
            'Ciclo escolar eliminado correctamente.'
        );
    }

    public function restore(int $id): JsonResponse
    {
        $cicloEscolar = $this->cicloEscolarService->restore($id);

        if (!$cicloEscolar) {
            return ResponseHelper::error(
                'Ciclo escolar no encontrado o no eliminado.',
                404
            );
        }

        return ResponseHelper::success(
            new CicloEscolarResource($cicloEscolar),
            'Ciclo escolar restaurado correctamente.'
        );
    }
}
