<?php

namespace App\Http\Controllers\School;

use Illuminate\Http\Request;
use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\School\ConceptoCicloEscolar;
use App\Services\School\ConceptoCicloEscolarService;
use App\Http\Resources\School\ConceptoCicloEscolarResource;
use App\Http\Requests\School\StoreConceptoCicloEscolarRequest;
use App\Http\Requests\School\UpdateConceptoCicloEscolarRequest;

class ConceptoCicloEscolarController extends Controller
{
    public function __construct(
        protected ConceptoCicloEscolarService $conceptoCicloEscolarService
    ) {}

    public function index(Request $request)
    {
        $conceptos = $this->conceptoCicloEscolarService->getAll(
            $request->all()
        );

        return ResponseHelper::success(
            ConceptoCicloEscolarResource::collection($conceptos),
            'Conceptos por ciclo escolar obtenidos correctamente'
        );
    }

    public function store(StoreConceptoCicloEscolarRequest $request)
    {
        $concepto = $this->conceptoCicloEscolarService->create(
            $request->validated()
        );

        return ResponseHelper::success(
            new ConceptoCicloEscolarResource($concepto),
            'Concepto asignado al ciclo escolar correctamente',
            201
        );
    }

    public function show(ConceptoCicloEscolar $conceptoCicloEscolar)
    {
        $conceptoCicloEscolar->load([
            'concepto',
            'cicloEscolar',
        ]);

        return ResponseHelper::success(
            new ConceptoCicloEscolarResource($conceptoCicloEscolar),
            'Concepto por ciclo escolar obtenido correctamente'
        );
    }

    public function update(UpdateConceptoCicloEscolarRequest $request, ConceptoCicloEscolar $conceptoCicloEscolar) {
        $concepto = $this->conceptoCicloEscolarService->update(
            $conceptoCicloEscolar,
            $request->validated()
        );

        return ResponseHelper::success(
            new ConceptoCicloEscolarResource($concepto),
            'Concepto por ciclo escolar actualizado correctamente'
        );
    }

    public function destroy(ConceptoCicloEscolar $conceptoCicloEscolar)
    {
        $this->conceptoCicloEscolarService->delete(
            $conceptoCicloEscolar
        );

        return ResponseHelper::success(
            null,
            'Concepto por ciclo escolar eliminado correctamente'
        );
    }

    public function restore(int $id)
    {
        $concepto = $this->conceptoCicloEscolarService->restore($id);

        return ResponseHelper::success(
            new ConceptoCicloEscolarResource($concepto),
            'Concepto por ciclo escolar restaurado correctamente'
        );
    }
}
