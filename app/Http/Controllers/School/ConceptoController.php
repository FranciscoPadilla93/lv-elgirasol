<?php

namespace App\Http\Controllers\School;

use App\Models\School\Concepto;
use Illuminate\Http\Request;
use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Services\School\ConceptoService;
use App\Http\Resources\School\ConceptoResource;
use App\Http\Requests\School\StoreConceptoRequest;
use App\Http\Requests\School\UpdateConceptoRequest;

class ConceptoController extends Controller
{
    public function __construct(
        protected ConceptoService $conceptoService
    ) {}

    public function index(Request $request)
    {
        $conceptos = $this->conceptoService->getAll(
            $request->all()
        );

        return ResponseHelper::success(
            ConceptoResource::collection($conceptos),
            'Conceptos obtenidos correctamente'
        );
    }

    public function store(StoreConceptoRequest $request)
    {
        $concepto = $this->conceptoService->create(
            $request->validated()
        );

        return ResponseHelper::success(
            new ConceptoResource($concepto),
            'Concepto creado correctamente',
            201
        );
    }

    public function show(Concepto $concepto)
    {
        return ResponseHelper::success(
            new ConceptoResource($concepto),
            'Concepto obtenido correctamente'
        );
    }

    public function update(UpdateConceptoRequest $request, Concepto $concepto) {
        $concepto = $this->conceptoService->update(
            $concepto,
            $request->validated()
        );

        return ResponseHelper::success(
            new ConceptoResource($concepto),
            'Concepto actualizado correctamente'
        );
    }

    public function destroy(Concepto $concepto)
    {
        $this->conceptoService->delete($concepto);

        return ResponseHelper::success(
            null,
            'Concepto eliminado correctamente'
        );
    }

    public function restore(int $id)
    {
        $concepto = $this->conceptoService->restore($id);

        return ResponseHelper::success(
            new ConceptoResource($concepto),
            'Concepto restaurado correctamente'
        );
    }
}
