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
        $expedientes = $this->expedienteService->paginate($request->validated());

        return ResponseHelper::success(
            ExpedienteResource::collection($expedientes),
            'Expedientes obtenidos correctamente.'
        );
    }

    public function show(Expediente $expediente): JsonResponse {
        $expediente = $this->expedienteService->find($expediente);

        return ResponseHelper::success(
            new ExpedienteResource($expediente),
            'Expediente obtenido correctamente.'
        );
    }

    public function store(StoreExpedienteRequest $request): JsonResponse {
        $data = $request->validated();
        $data['foto'] = $request->allFiles()['foto'] ?? null;
        $expediente = $this->expedienteService->create($data);

        return ResponseHelper::success(
            new ExpedienteResource(
                $expediente
            ),
            'Expediente creado correctamente.',
            201
        );
    }

    public function update(UpdateExpedienteRequest $request, Expediente $expediente): JsonResponse {
        $data = $request->validated();
        $data['foto'] = $request->allFiles()['foto'] ?? null;

        $expediente = $this->expedienteService->update(
            $expediente,
            $data
        );

        return ResponseHelper::success(
            new ExpedienteResource($expediente),
            'Expediente actualizado correctamente.'
        );
    }

    public function destroy(Expediente $expediente): JsonResponse {
        $this->expedienteService->delete($expediente);

        return ResponseHelper::success(
            null,
            'Expediente eliminado correctamente.'
        );
    }

    public function restore(int $id): JsonResponse {
        $expediente = $this->expedienteService->restore($id);

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
