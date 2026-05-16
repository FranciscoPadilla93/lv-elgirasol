<?php

namespace App\Services\School;

use App\Models\School\Inscripcion;
use App\Models\School\ReglaInscripcion;
use App\Models\Catalogs\EstadoInscripcion;
use App\Services\School\WorkflowInscripcionService;
use Illuminate\Support\Facades\DB;
use Exception;

class InscripcionService
{
    public function __construct(
        private WorkflowInscripcionService $workflowInscripcionService
    ) {}

    private function relations(): array
    {
        return [
            'expediente',
            'nivel',
            'grado',
            'cicloEscolar',
            'estadoInscripcion',
        ];
    }

    public function create(array $data): Inscripcion
    {
        return DB::transaction(function () use ($data) {
            // VALIDAR DUPLICADO
            $exists = Inscripcion::query()
                ->where('expediente_id', $data['expediente_id'])
                ->where('ciclo_escolar_id', $data['ciclo_escolar_id'])
                ->whereNull('deleted_at')
                ->exists();

            if ($exists) {
                throw new Exception(
                    'El alumno ya cuenta con inscripción para este ciclo escolar.'
                );
            }

            // DETERMINAR SI ES NUEVA ADMISIÓN AUTOMÁTICAMENTE
            $isNewAdmission = $this->isNewAdmission($data);

            // BUSCAR REGLA
            $regla = ReglaInscripcion::query()
                ->active()
                ->where('nivel_id', $data['nivel_id'])
                ->where('grado_id', $data['grado_id'])
                // ->where(
                //     'is_new_admission',
                //     $data['is_new_admission']
                // )
                ->first();

            if (!$regla) {
                throw new Exception(
                    'No existe una regla de inscripción configurada.'
                );
            }

            // REQUIERE EVALUACIÓN
            if ($isNewAdmission) {
                $data['requires_evaluation'] = true;
                $data['requires_socioeconomic_study'] = true;
            } else {
                $data['requires_socioeconomic_study'] = $regla->requires_socioeconomic_study;
                $data['requires_evaluation'] = $regla->requires_evaluation;
            }


            $data['requires_treasury_validation'] = $regla->requires_treasury_validation;

            $data['is_new_admission'] = $isNewAdmission;

            // DETERMINAR ESTADO INICIAL
            $estadoCode = $this->resolveInitialStatus($data);

            $estado = EstadoInscripcion::query()
                ->where('code', $estadoCode)
                ->first();

            if (!$estado) {
                throw new Exception(
                    'Estado de inscripción inválido.'
                );
            }

            $data['estado_inscripcion_id'] = $estado->id;
            $data['inscription_date'] = now();
            $data['is_completed'] = false;
            $data['created_by'] = auth()->id();
            $inscripcion = Inscripcion::create($data);

            // REFRESCAR WORKFLOW CENTRAL
            $inscripcion = $this->workflowInscripcionService->refresh($inscripcion);

            return $inscripcion->load($this->relations());
        });
    }

    public function update(Inscripcion $inscripcion, array $data): Inscripcion
    {
        return DB::transaction(function () use ($inscripcion, $data)
        {
            // AUDITORÍA
            $data['updated_by'] = auth()->id();
            $inscripcion->update($data);
            $inscripcion->refresh();
            $inscripcion = $this->workflowInscripcionService->refresh($inscripcion);

            return $inscripcion->load($this->relations());
        });
    }

    public function delete(Inscripcion $inscripcion): void
    {
        DB::transaction(function () use ($inscripcion)
        {
            $inscripcion->delete();

            $inscripcion->update([
                'updated_by' => auth()->id()
            ]);
        });
    }

    public function restore(int $id): ?Inscripcion
    {
        return DB::transaction(function () use ($id) {
            $inscripcion = Inscripcion::withTrashed()->find($id);

            if (!$inscripcion || !$inscripcion->trashed()) {
                return null;
            }

            $inscripcion->restore();

            $inscripcion->update([
                'updated_by' => auth()->id()
            ]);

            return $inscripcion->load(
                $this->relations()
            );
        });
    }

    private function resolveInitialStatus(array $data): string
    {
        if ($data['requires_evaluation']) {
            return 'evaluation_pending';
        }

        if ($data['requires_treasury_validation']) {
            return 'payment_pending';
        }

        return 'approved';
    }

    private function isNewAdmission(array $data): bool
    {
        return ! Inscripcion::query()
            ->where('expediente_id', $data['expediente_id'])
            ->whereNull('deleted_at')
            ->exists();
    }
}
