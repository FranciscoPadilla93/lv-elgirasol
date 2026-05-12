<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\ResponseHelper;
use App\Http\Requests\School\StoreInscripcionRequest;
use App\Http\Requests\School\UpdateInscripcionRequest;
use App\Http\Resources\School\InscripcionResource;
use App\Models\School\Inscripcion;
use App\Services\School\InscripcionService;
use Illuminate\Http\JsonResponse;

class InscripcionController extends Controller
{
    public function __construct(
        private InscripcionService $inscripcionService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $search = $request->string('search')->toString();
        $perPage = $request->integer('per_page', 15);
        $sortBy = $request->string('sort_by')->toString() ?: 'id';
        $sortDirection = $request->string('sort_direction')->toString() ?: 'desc';
        $inscripciones = Inscripcion::query()->with([
                'expediente',
                'cicloEscolar',
                'nivel',
                'grado',
                'estadoInscripcion',
            ])
            // SEARCH
            ->when($search !== '', function ($query) use ($search) {
                $query->whereHas(
                    'expediente',
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
                $request->filled('ciclo_escolar_id'),
                function ($query) use ($request) {
                    $query->where(
                        'ciclo_escolar_id',
                        $request->ciclo_escolar_id
                    );
                }
            )
            ->when(
                $request->filled('nivel_id'),
                function ($query) use ($request) {
                    $query->where(
                        'nivel_id',
                        $request->nivel_id
                    );
                }
            )
            ->when(
                $request->filled('grado_id'),
                function ($query) use ($request) {
                    $query->where(
                        'grado_id',
                        $request->grado_id
                    );
                }
            )
            ->when(
                $request->filled('estado_inscripcion_id'),
                function ($query) use ($request) {
                    $query->where(
                        'estado_inscripcion_id',
                        $request->estado_inscripcion_id
                    );
                }
            )
            ->when(
                $request->filled('is_completed'),
                function ($query) use ($request) {
                    $query->where(
                        'is_completed',
                        filter_var(
                            $request->is_completed,
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
            InscripcionResource::collection($inscripciones),
            'Inscripciones obtenidas correctamente.'
        );
    }

    public function show(Inscripcion $inscripcion): JsonResponse
    {
        $inscripcion->load([
            'expediente',
            'cicloEscolar',
            'nivel',
            'grado',
            'estadoInscripcion',
            'evaluacionesIniciales',
            'estudiosSocioeconomicos',
        ]);

        return ResponseHelper::success(
            new InscripcionResource($inscripcion),
            'Inscripción obtenida correctamente.'
        );
    }

    public function store(StoreInscripcionRequest $request): JsonResponse
    {
        $inscripcion = $this->inscripcionService->create($request->validated());

        return ResponseHelper::success(
            new InscripcionResource($inscripcion),
            'Inscripción creada correctamente.',
            201
        );
    }

    public function update(UpdateInscripcionRequest $request,Inscripcion $inscripcion): JsonResponse
    {
        $inscripcion = $this->inscripcionService
            ->update(
                $inscripcion,
                $request->validated()
            );

        return ResponseHelper::success(
            new InscripcionResource($inscripcion),
            'Inscripción actualizada correctamente.'
        );
    }

    public function destroy(Inscripcion $inscripcion): JsonResponse
    {
        $this->inscripcionService->delete($inscripcion);

        return ResponseHelper::success(
            null,
            'Inscripción eliminada correctamente.'
        );
    }

    public function restore(int $id): JsonResponse
    {
        $inscripcion = $this->inscripcionService->restore($id);

        if (!$inscripcion) {
            return ResponseHelper::error(
                'Inscripción no encontrada o no eliminada.',
                404
            );
        }

        return ResponseHelper::success(
            new InscripcionResource($inscripcion),
            'Inscripción restaurada correctamente.'
        );
    }
}
