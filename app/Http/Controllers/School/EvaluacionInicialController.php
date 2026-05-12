<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Helpers\ResponseHelper;
use App\Http\Requests\School\StoreEvaluacionInicialRequest;
use App\Http\Requests\School\UpdateEvaluacionInicialRequest;
use App\Http\Resources\School\EvaluacionInicialResource;
use App\Models\School\EvaluacionInicial;
use App\Services\School\EvaluacionInicialService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EvaluacionInicialController extends Controller
{
    public function __construct(private EvaluacionInicialService $evaluacionInicialService) {}

    public function index(Request $request): JsonResponse
    {
        $search = $request->string('search')->toString();
        $perPage = $request->integer('per_page', 15);
        $sortBy = $request->string('sort_by')->toString() ?: 'id';
        $sortDirection = $request->string('sort_direction')->toString() ?: 'desc';

        $evaluaciones = EvaluacionInicial::query()
            ->with([
                'inscripcion.expediente',
                'tipoEvaluacion',
                'evaluator',
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
                $request->filled('tipo_evaluacion_id'),
                function ($query) use ($request) {
                    $query->where(
                        'tipo_evaluacion_id',
                        $request->tipo_evaluacion_id
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
            EvaluacionInicialResource::collection($evaluaciones),
            'Evaluaciones iniciales obtenidas correctamente.'
        );
    }

    public function show(EvaluacionInicial $evaluacionInicial): JsonResponse {
        $evaluacionInicial->load([
            'inscripcion.expediente',
            'tipoEvaluacion',
            'evaluator',
        ]);

        return ResponseHelper::success(
            new EvaluacionInicialResource($evaluacionInicial),
            'Evaluación inicial obtenida correctamente.'
        );
    }

    public function store(StoreEvaluacionInicialRequest $request): JsonResponse {
        $evaluacion = $this->evaluacionInicialService->create($request->validated());

        return ResponseHelper::success(
            new EvaluacionInicialResource($evaluacion),
            'Evaluación inicial creada correctamente.',
            201
        );
    }

    public function update(UpdateEvaluacionInicialRequest $request, EvaluacionInicial $evaluacionInicial): JsonResponse {
        $evaluacion = $this->evaluacionInicialService
            ->update(
                $evaluacionInicial,
                $request->validated()
            );

        return ResponseHelper::success(
            new EvaluacionInicialResource($evaluacion),
            'Evaluación inicial actualizada correctamente.'
        );
    }

    public function destroy(EvaluacionInicial $evaluacionInicial): JsonResponse {
        $this->evaluacionInicialService->delete($evaluacionInicial);

        return ResponseHelper::success(
            null,
            'Evaluación inicial eliminada correctamente.'
        );
    }

    public function restore(int $id): JsonResponse
    {
        $evaluacion = $this->evaluacionInicialService->restore($id);

        if (!$evaluacion) {
            return ResponseHelper::error(
                'Evaluación inicial no encontrada o no eliminada.',
                404
            );
        }

        return ResponseHelper::success(
            new EvaluacionInicialResource($evaluacion),
            'Evaluación inicial restaurada correctamente.'
        );
    }
}
