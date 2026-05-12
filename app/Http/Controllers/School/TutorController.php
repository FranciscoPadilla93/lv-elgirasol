<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Helpers\ResponseHelper;
use App\Http\Requests\School\StoreTutorRequest;
use App\Http\Requests\School\UpdateTutorRequest;
use App\Http\Resources\School\TutorResource;
use App\Models\School\Tutor;
use App\Services\School\TutorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TutorController extends Controller
{
    public function __construct(
        private TutorService $tutorService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $search = $request->string('search')->toString();
        $perPage = $request->integer('per_page', 15);
        $sortBy = $request->string('sort_by')->toString() ?: 'id';
        $sortDirection = $request->string('sort_direction')->toString() ?: 'desc';
        $tutores = Tutor::query()
            ->with([
                'genero',
                'user',
            ])
            // SEARCH
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {

                    $query->where('nombre', 'LIKE', "%{$search}%")
                        ->orWhere('apellido_paterno', 'LIKE', "%{$search}%")
                        ->orWhere('apellido_materno', 'LIKE', "%{$search}%")
                        ->orWhere('correo', 'LIKE', "%{$search}%")
                        ->orWhere('telefono', 'LIKE', "%{$search}%")
                        ->orWhere('curp', 'LIKE', "%{$search}%");
                });
            })
            // FILTROS
            ->when(
                $request->filled('genero_id'),
                function ($query) use ($request) {

                    $query->where(
                        'genero_id',
                        $request->genero_id
                    );
                }
            )
            // ORDENAMIENTO
            ->orderBy($sortBy, $sortDirection)
            // PAGINACIÓN
            ->paginate($perPage);

        return ResponseHelper::success(
            TutorResource::collection($tutores),
            'Tutores obtenidos correctamente.'
        );
    }

    public function show(Tutor $tutor): JsonResponse
    {
        $tutor->load([
            'genero',
            'user',
            'expedienteTutores.parentesco',
            'expedientes',
        ]);

        return ResponseHelper::success(
            new TutorResource($tutor),
            'Tutor obtenido correctamente.'
        );
    }

    public function store(StoreTutorRequest $request): JsonResponse
    {
        $tutor = $this->tutorService->create(
                $request->validated()
            );

        return ResponseHelper::success(
            new TutorResource($tutor),
            'Tutor creado correctamente.',
            201
        );
    }

    public function update(UpdateTutorRequest $request, Tutor $tutor): JsonResponse {
        $tutor = $this->tutorService
            ->update(
                $tutor,
                $request->validated()
            );

        return ResponseHelper::success(
            new TutorResource($tutor),
            'Tutor actualizado correctamente.'
        );
    }

    public function destroy(Tutor $tutor): JsonResponse
    {
        $this->tutorService->delete($tutor);

        return ResponseHelper::success(
            null,
            'Tutor eliminado correctamente.'
        );
    }

    public function restore(int $id): JsonResponse
    {
        $tutor = $this->tutorService->restore($id);

        if (!$tutor) {
            return ResponseHelper::error(
                'Tutor no encontrado o no eliminado.',
                404
            );
        }

        return ResponseHelper::success(
            new TutorResource($tutor),
            'Tutor restaurado correctamente.'
        );
    }
}
