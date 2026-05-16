<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\ResponseHelper;
use App\Http\Requests\School\StoreExpedienteContactoRequest;
use App\Http\Requests\School\UpdateExpedienteContactoRequest;
use App\Http\Resources\School\ExpedienteContactoResource;
use App\Models\School\Expediente;
use App\Models\School\ExpedienteContacto;
use App\Services\School\ExpedienteContactoService;
use Illuminate\Http\JsonResponse;

class ExpedienteContactoController extends Controller
{
    public function __construct(
        private readonly ExpedienteContactoService $expedienteContactoService
    ) {}

    public function index(Expediente $expediente): JsonResponse {
        $contactos = $expediente
            ->contactos()
            ->with([
                'parentesco',
            ])
            ->latest()
            ->paginate(10);

        return ResponseHelper::success(
            ExpedienteContactoResource::collection($contactos)
        );
    }

    public function store(StoreExpedienteContactoRequest $request,Expediente $expediente): JsonResponse {
        $contacto = $this->expedienteContactoService
            ->create(
                $expediente,
                $request->validated()
            );

        return ResponseHelper::success(
            new ExpedienteContactoResource($contacto),
            'Contacto creado correctamente.',
            201
        );
    }

    public function show(Expediente $expediente, ExpedienteContacto $contacto): JsonResponse {
        $contacto->load(['parentesco',]);

        return ResponseHelper::success(
            new ExpedienteContactoResource($contacto)
        );
    }

    public function update(UpdateExpedienteContactoRequest $request, ExpedienteContacto $contacto): JsonResponse {
        $contacto = $this->expedienteContactoService
            ->update(
                $contacto,
                $request->validated()
            );

        return ResponseHelper::success(
            new ExpedienteContactoResource($contacto),
            'Contacto actualizado correctamente.'
        );
    }

    public function destroy(ExpedienteContacto $contacto): JsonResponse {
        $this->expedienteContactoService
            ->delete($contacto);

        return ResponseHelper::success(
            null,
            'Contacto eliminado correctamente.'
        );
    }

    public function restore(int $id): JsonResponse {
        $contacto = $this->expedienteContactoService->restore($id);

        if (!$contacto) {

            return ResponseHelper::error(
                'El contacto no existe o no está eliminado.',
                404
            );
        }

        return ResponseHelper::success(
            new ExpedienteContactoResource($contacto),
            'Contacto restaurado correctamente.'
        );
    }
}
