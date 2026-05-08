<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Helpers\ResponseHelper;
// use Illuminate\Http\Request;
use App\Http\Requests\School\IndexExpedienteRequest;
use App\Http\Requests\School\StoreExpedienteRequest;
use App\Http\Requests\School\UpdateExpedienteRequest;
use App\Http\Resources\School\ExpedienteResource;
use App\Models\School\Expediente;
use App\Services\School\ExpedienteService;
use Illuminate\Http\JsonResponse;

class ExpedienteController extends Controller
{
    public function __construct(
        private ExpedienteService $expedienteService
    ) {}

    public function index(IndexExpedienteRequest $request): JsonResponse {
        $search = $request->string('search')->toString();
        $perPage = $request->integer('per_page', 15);
        $sortBy = $request->string('sort_by')->toString() ?: 'id';
        $sortDirection = $request->string('sort_direction')->toString() ?: 'desc';
        $expedientes = Expediente::query()
            ->with([
                'genero',
                'estadoExpediente',
            ])

            // SEARCH
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('folio', 'LIKE', "%{$search}%")
                        ->orWhere('nombre', 'LIKE', "%{$search}%")
                        ->orWhere('apellido_paterno', 'LIKE', "%{$search}%")
                        ->orWhere('apellido_materno', 'LIKE', "%{$search}%")
                        ->orWhere('curp', 'LIKE', "%{$search}%");
                });
            })

            // FILTROS
            ->when(
                $request->filled('estado_expediente_id'),
                function ($query) use ($request) {
                    $query->where(
                        'estado_expediente_id',
                        $request->estado_expediente_id
                    );
                }
            )

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
            ExpedienteResource::collection($expedientes),
            'Expedientes obtenidos correctamente.'
        );
    }

    public function show(Expediente $expediente): JsonResponse {
        $expediente->load([
            'genero',
            'estadoExpediente',
            'documentos.tipoDocumento',
            'contactos.parentesco',
            'inscripciones',
            'expedienteTutores.tutor',
        ]);

        return ResponseHelper::success(
            new ExpedienteResource($expediente),
            'Expediente obtenido correctamente.'
        );
    }

    public function store(StoreExpedienteRequest $request): JsonResponse {
        $data = $request->validated();

        $data['foto'] = $request->allFiles()['foto'] ?? null;

        $expediente = $this->expedienteService
            ->create($data);

        return ResponseHelper::success(
            new ExpedienteResource(
                $expediente
            ),
            'Expediente creado correctamente.',
            201
        );
    }

    public function update(UpdateExpedienteRequest $request,Expediente $expediente): JsonResponse {
        $expediente = $this->expedienteService
            ->update(
                $expediente,
                $request->validated()
            );

        return ResponseHelper::success(
            new ExpedienteResource($expediente),
            'Expediente actualizado correctamente.'
        );
    }

    public function destroy(Expediente $expediente): JsonResponse {
        $this->expedienteService
            ->delete($expediente);

        return ResponseHelper::success(
            null,
            'Expediente eliminado correctamente.'
        );
    }

    public function restore(int $id): JsonResponse {
        $expediente = $this->expedienteService
            ->restore($id);

        if (!$expediente) {
            return ResponseHelper::error(
                'Expediente no encontrado o no eliminado.',
                404
            );
        }

        return ResponseHelper::success(
            new ExpedienteResource($expediente),
            'Expediente restaurado correctamente.'
        );
    }
}
