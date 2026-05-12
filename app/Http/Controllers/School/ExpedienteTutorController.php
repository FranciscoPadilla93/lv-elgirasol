<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Helpers\ResponseHelper;
use App\Http\Requests\School\StoreExpedienteTutorRequest;
use App\Http\Requests\School\UpdateExpedienteTutorRequest;
use App\Http\Resources\School\ExpedienteTutorResource;
use App\Models\School\ExpedienteTutor;
use App\Services\School\ExpedienteTutorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExpedienteTutorController extends Controller
{
    public function __construct(
        private ExpedienteTutorService $expedienteTutorService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $search = $request->string('search')->toString();
        $perPage = $request->integer('per_page', 15);
        $sortBy = $request->string('sort_by')->toString() ?: 'id';
        $sortDirection = $request->string('sort_direction')->toString() ?: 'desc';

        $expedienteTutores = ExpedienteTutor::query()
            ->with([
                'expediente',
                'tutor',
                'parentesco',
            ])
            // SEARCH
            ->when($search !== '', function ($query) use ($search) {
                $query->whereHas('tutor', function ($query) use ($search) {
                    $query->where('nombre', 'LIKE', "%{$search}%")
                        ->orWhere('apellido_paterno', 'LIKE', "%{$search}%")
                        ->orWhere('apellido_materno', 'LIKE', "%{$search}%")
                        ->orWhere('correo', 'LIKE', "%{$search}%")
                        ->orWhere('telefono', 'LIKE', "%{$search}%");
                });
            })
            // FILTROS
            ->when(
                $request->filled('expediente_id'),
                function ($query) use ($request) {

                    $query->where(
                        'expediente_id',
                        $request->expediente_id
                    );
                }
            )
            ->when(
                $request->filled('tutor_id'),
                function ($query) use ($request) {

                    $query->where(
                        'tutor_id',
                        $request->tutor_id
                    );
                }
            )
            ->when(
                $request->filled('parentesco_id'),
                function ($query) use ($request) {

                    $query->where(
                        'parentesco_id',
                        $request->parentesco_id
                    );
                }
            )
            // ORDENAMIENTO
            ->orderBy($sortBy, $sortDirection)
            // PAGINACIÓN
            ->paginate($perPage);

        return ResponseHelper::success(
            ExpedienteTutorResource::collection($expedienteTutores),
            'Relaciones expediente tutor obtenidas correctamente.'
        );
    }

    public function show(ExpedienteTutor $expedienteTutor): JsonResponse {
        $expedienteTutor->load([
            'expediente',
            'tutor',
            'parentesco',
        ]);

        return ResponseHelper::success(
            new ExpedienteTutorResource($expedienteTutor),
            'Relación expediente tutor obtenida correctamente.'
        );
    }

    public function store(StoreExpedienteTutorRequest $request): JsonResponse {
        $expedienteTutor = $this->expedienteTutorService->create($request->validated());

        return ResponseHelper::success(
            new ExpedienteTutorResource($expedienteTutor),
            'Tutor asociado correctamente al expediente.',
            201
        );
    }

    public function update(UpdateExpedienteTutorRequest $request,ExpedienteTutor $expedienteTutor): JsonResponse {
        $expedienteTutor = $this->expedienteTutorService
            ->update($expedienteTutor,$request->validated());

        return ResponseHelper::success(
            new ExpedienteTutorResource($expedienteTutor),
            'Relación expediente tutor actualizada correctamente.'
        );
    }

    public function destroy(ExpedienteTutor $expedienteTutor): JsonResponse {
        $this->expedienteTutorService->delete($expedienteTutor);

        return ResponseHelper::success(
            null,
            'Relación expediente tutor eliminada correctamente.'
        );
    }

    public function restore(int $id): JsonResponse
    {
        $expedienteTutor = $this->expedienteTutorService->restore($id);

        if (!$expedienteTutor) {
            return ResponseHelper::error(
                'Relación expediente tutor no encontrada o no eliminada.',
                404
            );
        }

        return ResponseHelper::success(
            new ExpedienteTutorResource($expedienteTutor),
            'Relación expediente tutor restaurada correctamente.'
        );
    }
}
