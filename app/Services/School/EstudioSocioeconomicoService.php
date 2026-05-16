<?php

namespace App\Services\School;

use App\Models\School\EstudioSocioeconomico;
use App\Services\School\WorkflowInscripcionService;
use Illuminate\Support\Facades\DB;
use Exception;

class EstudioSocioeconomicoService
{
    public function __construct(
        private WorkflowInscripcionService $workflowInscripcionService
    ) {}

    private function relations(): array
    {
        return [
            'inscripcion.expediente',
            'approver',
        ];
    }

    public function create(array $data): EstudioSocioeconomico
    {
        return DB::transaction(function () use ($data) {
            // INSCRIPCIÓN
            $inscripcion = $this->getInscripcion($data);

            // VALIDAR REQUIERE ESTUDIO
            if (!$inscripcion->requires_socioeconomic_study) {
                throw new Exception(
                    'La inscripción no requiere estudio socioeconómico.'
                );
            }
            // SUBMITTED AT
            $data['submitted_at'] = now();
            // APROBACIÓN
            if (!empty($data['is_approved'])) {
                $data['approved_at'] = now();
                $data['approved_by'] = auth()->id();
            }
            // AUDITORÍA
            $data['created_by'] = auth()->id();
            $estudio = EstudioSocioeconomico::create($data);

            // ACTUALIZAR FLAG
            $inscripcion->socioeconomic_study_approved = (bool) $estudio->is_approved;
            $inscripcion->save();

            // ACTUALIZAR WORKFLOW CENTRAL
            $this->workflowInscripcionService
                ->refresh($inscripcion);

            return $estudio->load($this->relations());
        });
    }

    public function update(EstudioSocioeconomico $estudioSocioeconomico, array $data): EstudioSocioeconomico {
        return DB::transaction(function () use ($estudioSocioeconomico, $data) {
            // APROBACIÓN
            if (isset($data['is_approved']) && $data['is_approved']) {
                $data['approved_at'] = now();
                $data['approved_by'] = auth()->id();
            }

            // SI SE RECHAZA / DESAPRUEBA, LIMPIAR APROBACIÓN
            if (array_key_exists('is_approved', $data) && !$data['is_approved']) {
                $data['approved_at'] = null;
                $data['approved_by'] = null;
            }

            // AUDITORÍA
            $data['updated_by'] = auth()->id();
            $estudioSocioeconomico->update($data);
            $estudioSocioeconomico->refresh();

            $inscripcion = $estudioSocioeconomico->inscripcion;

            // ACTUALIZAR FLAG
            $inscripcion->socioeconomic_study_approved = (bool) $estudioSocioeconomico->is_approved;
            $inscripcion->save();

            // ACTUALIZAR WORKFLOW CENTRAL
            $this->workflowInscripcionService->refresh($inscripcion);
            return $estudioSocioeconomico->load($this->relations());
        });
    }

    public function delete(EstudioSocioeconomico $estudioSocioeconomico): void {
        DB::transaction(function () use ($estudioSocioeconomico) {
            $estudioSocioeconomico->delete();

             $estudioSocioeconomico->update([
                'status' => false,
                'updated_by' => auth()->id()
            ]);
        });
    }

    public function restore(int $id): ?EstudioSocioeconomico
    {
        return DB::transaction(function () use ($id) {
            $estudio = EstudioSocioeconomico::withTrashed()->find($id);

            if (!$estudio || !$estudio->trashed()) {
                return null;
            }

            $estudio->restore();

            $estudio->update([
                'status' => true,
                'updated_by' => auth()->id()
            ]);

            return $estudio->load(
                $this->relations()
            );
        });
    }

    private function getInscripcion(array $data)
    {
        return \App\Models\School\Inscripcion::query()
            ->findOrFail($data['inscripcion_id']);
    }
}
