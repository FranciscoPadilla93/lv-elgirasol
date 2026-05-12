<?php

namespace App\Services\School;

use App\Models\School\EstudioSocioeconomico;
use App\Models\Catalogs\EstadoInscripcion;
use Illuminate\Support\Facades\DB;
use Exception;

class EstudioSocioeconomicoService
{
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

            // ACTUALIZAR WORKFLOW
            $this->updateInscripcionWorkflow(
                $inscripcion,
                $estudio
            );

            return $estudio->load(
                $this->relations()
            );
        });
    }

    public function update(EstudioSocioeconomico $estudioSocioeconomico, array $data): EstudioSocioeconomico {
        return DB::transaction(function () use ($estudioSocioeconomico, $data) {
            // APROBACIÓN
            if (isset($data['is_approved']) && $data['is_approved']) {
                $data['approved_at'] = now();
                $data['approved_by'] = auth()->id();
            }

            // AUDITORÍA
            $data['updated_by'] = auth()->id();
            $estudioSocioeconomico->update($data);
            $estudioSocioeconomico->refresh();

            // ACTUALIZAR WORKFLOW
            $this->updateInscripcionWorkflow(
                $estudioSocioeconomico->inscripcion,
                $estudioSocioeconomico
            );

            return $estudioSocioeconomico->load(
                $this->relations()
            );
        });
    }

    public function delete(EstudioSocioeconomico $estudioSocioeconomico): void {
        $estudioSocioeconomico->delete();
    }

    public function restore(int $id): ?EstudioSocioeconomico
    {
        return DB::transaction(function () use ($id) {
            $estudio = EstudioSocioeconomico::withTrashed()->find($id);

            if (!$estudio || !$estudio->trashed()) {
                return null;
            }

            $estudio->restore();

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

    private function updateInscripcionWorkflow($inscripcion, EstudioSocioeconomico $estudio): void {
        // ACTUALIZAR FLAG
        $inscripcion->socioeconomic_study_approved = $estudio->is_approved;

        // ESTADO
        $estadoCode = $estudio->is_approved
            ? 'payment_pending'
            : 'documents_pending';

        // OBTENER ESTADO
        $estado = EstadoInscripcion::query()
            ->where('code', $estadoCode)
            ->first();

        if (!$estado) {
            throw new Exception(
                'Estado de inscripción inválido.'
            );
        }

        $inscripcion->estado_inscripcion_id = $estado->id;

        $inscripcion->save();
    }
}
