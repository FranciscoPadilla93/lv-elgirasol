<?php

namespace App\Services\School;

use App\Models\School\EvaluacionInicial;
use App\Models\School\Inscripcion;
use App\Models\School\ReglaInscripcion;
use App\Models\Catalogs\TipoEvaluacion;
use App\Services\School\WorkflowInscripcionService;
use Illuminate\Support\Facades\DB;
use Exception;

class EvaluacionInicialService
{
    public function __construct(
        private WorkflowInscripcionService $workflowInscripcionService
    ) {}

    private function relations(): array
    {
        return [
            'inscripcion',
            'tipoEvaluacion',
            'evaluator',
        ];
    }

    public function create(array $data): EvaluacionInicial
    {
        return DB::transaction(function () use ($data) {
            // INSCRIPCIÓN
            $inscripcion = $this->getInscripcion($data);

            // VALIDAR REQUIERE EVALUACIÓN
            if (!$inscripcion->requires_evaluation) {
                throw new Exception(
                    'La inscripción no requiere evaluación.'
                );
            }

            // OBTENER REGLA
            $regla = $this->getRegla($inscripcion);
             // TIPO EVALUACIÓN
            $tipoEvaluacion = TipoEvaluacion::query()
                ->active()
                ->findOrFail($data['tipo_evaluacion_id']);

            // VALIDAR TIPO REQUERIDO/PERMITIDO
            $this->validateTipoEvaluacionPermitido(
                $regla,
                $tipoEvaluacion
            );

            // VALIDACION EVALUACION APROBADA
            $this->validateNoApprovedEvaluationExists(
                $inscripcion,
                $tipoEvaluacion
            );

            // CALCULAR INTENTO POR TIPO DE EVALUACIÓN
             $attempt = EvaluacionInicial::query()
                ->where('inscripcion_id', $inscripcion->id)
                ->where('tipo_evaluacion_id', $tipoEvaluacion->id)
                ->max('attempt');

            $data['attempt'] = ($attempt ?? 0) + 1;

            // APROBACIÓN AUTOMÁTICA SI EXISTE SCORE MÍNIMO
            $data['is_approved'] = $data['is_approved'] ?? false;
            if ($regla->minimum_score !== null) {
                $data['is_approved'] = (
                    $data['score'] >= $regla->minimum_score
                );
            }
            // AUDITORÍA
            $data['created_by'] = auth()->id();
            $evaluacion = EvaluacionInicial::create($data);

            // ACTUALIZAR INSCRIPCIÓN
            $this->workflowInscripcionService->refresh($inscripcion);

            return $evaluacion->load(
                $this->relations()
            );
        });
    }

    public function update(EvaluacionInicial $evaluacionInicial, array $data): EvaluacionInicial {

        return DB::transaction(function () use ($evaluacionInicial, $data) {
            $inscripcion = $evaluacionInicial->inscripcion;

            // SI CAMBIA INSCRIPCIÓN
            if (isset($data['inscripcion_id'])) {
                $inscripcion = Inscripcion::query()
                    ->findOrFail($data['inscripcion_id']);
            }

            // REGLA
            $regla = $this->getRegla($inscripcion);

            // SI CAMBIA TIPO DE EVALUACIÓN, VALIDAR
            if (isset($data['tipo_evaluacion_id'])) {
                $tipoEvaluacion = TipoEvaluacion::query()
                    ->active()
                    ->findOrFail($data['tipo_evaluacion_id']);

                $this->validateTipoEvaluacionPermitido(
                    $regla,
                    $tipoEvaluacion
                );
            }

            // RECALCULAR APROBACIÓN SI CAMBIA SCORE Y HAY MÍNIMO
            if (isset($data['score'])) {
                if ($regla->minimum_score !== null) {
                    $data['is_approved'] = (
                        $data['score'] >= $regla->minimum_score
                    );
                }
            }

            // AUDITORÍA
            $data['updated_by'] = auth()->id();
            $evaluacionInicial->update($data);
            $evaluacionInicial->refresh();

            // ACTUALIZAR WORKFLOW CENTRAL
            $this->workflowInscripcionService->refresh($inscripcion);

            return $evaluacionInicial->load(
                $this->relations()
            );
        });
    }

    public function delete(EvaluacionInicial $evaluacionInicial): void {
        DB::transaction(function () use ($evaluacionInicial) {
            $evaluacionInicial->update([
                'status' => false,
                'updated_by' => auth()->id()
            ]);
            $evaluacionInicial->delete();
        });
    }

    public function restore(int $id): ?EvaluacionInicial
    {
        return DB::transaction(function () use ($id) {
            $evaluacion = EvaluacionInicial::withTrashed() ->find($id);

            if (!$evaluacion || !$evaluacion->trashed()) {
                return null;
            }

            $evaluacion->restore();

            $evaluacion->update([
                'status' => true,
                'updated_by' => auth()->id()
            ]);
            return $evaluacion->load(
                $this->relations()
            );
        });
    }

    private function getInscripcion(array $data)
    {
        return \App\Models\School\Inscripcion::query()
            ->findOrFail($data['inscripcion_id']);
    }

    private function getRegla($inscripcion)
    {
        $regla = ReglaInscripcion::query()
            ->active()
            ->where('nivel_id', $inscripcion->nivel_id)
            ->where('grado_id', $inscripcion->grado_id)
            ->first();

        if (!$regla) {

            throw new Exception(
                'No existe una regla de inscripción configurada.'
            );
        }

        return $regla;
    }

    private function validateNoApprovedEvaluationExists(Inscripcion $inscripcion, TipoEvaluacion $tipoEvaluacion): void {
        $exists = EvaluacionInicial::query()
            ->where('inscripcion_id', $inscripcion->id)
            ->where('tipo_evaluacion_id', $tipoEvaluacion->id)
            ->where('is_approved', true)
            ->exists();

        if ($exists) {
            throw new Exception(
                'Ya existe una evaluación aprobada de este tipo para la inscripción.'
            );
        }
    }

    private function validateTipoEvaluacionPermitido(ReglaInscripcion $regla, TipoEvaluacion $tipoEvaluacion): void {
        $requiredEvaluations = $regla->required_evaluations ?? [];

        if (!empty($requiredEvaluations) && !in_array($tipoEvaluacion->code, $requiredEvaluations)
        ) {
            throw new Exception(
                'El tipo de evaluación no está permitido para esta inscripción.'
            );
        }
    }
}
