<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Helpers\ResponseHelper;
use App\Http\Requests\School\StoreEstudioSocioeconomicoRequest;
use App\Http\Requests\School\UpdateEstudioSocioeconomicoRequest;
use App\Http\Resources\School\EstudioSocioeconomicoResource;
use App\Models\School\EstudioSocioeconomico;
use App\Services\School\EstudioSocioeconomicoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EstudioSocioeconomicoController extends Controller
{
    public function __construct(
        private EstudioSocioeconomicoService $estudioSocioeconomicoService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $search = $request->string('search')->toString();
        $perPage = $request->integer('per_page', 15);
        $sortBy = $request->string('sort_by')->toString() ?: 'id';
        $sortDirection = $request->string('sort_direction')->toString() ?: 'desc';

        $estudios = EstudioSocioeconomico::query()
            ->with([
                'inscripcion.expediente',
                'approver',
            ])
            // SEARCH
            ->when($search !== '', function ($query) use ($search) {
                $query->whereHas(
                    'inscripcion.expediente',
                    function ($query) use ($search) {
                        $query->where('folio', 'LIKE', "%{$search}%")
                            ->orWhere('nombre', 'LIKE', "%{$search}%")
                            ->orWhere('apellido_paterno', 'LIKE', "%{$search}%")
                            ->orWhere('apellido_materno', 'LIKE', "%{$search}%")
                            ->orWhere('curp', 'LIKE', "%{$search}%");
                    }
                );
            })
            // FILTROS
            ->when(
                $request->filled('inscripcion_id'),
                function ($query) use ($request) {
                    $query->where(
                        'inscripcion_id',
                        $request->inscripcion_id
                    );
                }
            )
            ->when(
                $request->filled('is_approved'),
                function ($query) use ($request) {
                    $query->where(
                        'is_approved',
                        filter_var(
                            $request->is_approved,
                            FILTER_VALIDATE_BOOLEAN
                        )
                    );
                }
            )
            ->when(
                $request->filled('submitted_by_tutor'),
                function ($query) use ($request) {
                    $query->where(
                        'submitted_by_tutor',
                        filter_var(
                            $request->submitted_by_tutor,
                            FILTER_VALIDATE_BOOLEAN
                        )
                    );
                }
            )
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
            EstudioSocioeconomicoResource::collection($estudios),
            'Estudios socioeconómicos obtenidos correctamente.'
        );
    }

    public function show(EstudioSocioeconomico $estudioSocioeconomico): JsonResponse {
        $estudioSocioeconomico->load([
            'inscripcion.expediente',
            'approver',
        ]);

        return ResponseHelper::success(
            new EstudioSocioeconomicoResource($estudioSocioeconomico),
            'Estudio socioeconómico obtenido correctamente.'
        );
    }

    public function store(StoreEstudioSocioeconomicoRequest $request): JsonResponse
    {
        $estudio = $this->estudioSocioeconomicoService
            ->create(
                $request->validated()
            );

        return ResponseHelper::success(
            new EstudioSocioeconomicoResource($estudio),
            'Estudio socioeconómico creado correctamente.',
            201
        );
    }

    public function update(UpdateEstudioSocioeconomicoRequest $request,EstudioSocioeconomico $estudioSocioeconomico): JsonResponse
    {
        $estudio = $this->estudioSocioeconomicoService
            ->update(
                $estudioSocioeconomico,
                $request->validated()
            );

        return ResponseHelper::success(
            new EstudioSocioeconomicoResource($estudio),
            'Estudio socioeconómico actualizado correctamente.'
        );
    }

    public function destroy(EstudioSocioeconomico $estudioSocioeconomico): JsonResponse
    {
        $this->estudioSocioeconomicoService->delete($estudioSocioeconomico);

        return ResponseHelper::success(
            null,
            'Estudio socioeconómico eliminado correctamente.'
        );
    }

    public function restore(int $id): JsonResponse
    {
        $estudio = $this->estudioSocioeconomicoService->restore($id);

        if (!$estudio) {
            return ResponseHelper::error(
                'Estudio socioeconómico no encontrado o no eliminado.',
                404
            );
        }

        return ResponseHelper::success(
            new EstudioSocioeconomicoResource($estudio),
            'Estudio socioeconómico restaurado correctamente.'
        );
    }
}
