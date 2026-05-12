<?php

namespace App\Services\School;

use App\Models\School\Inscripcion;
use App\Models\School\ReglaInscripcion;
use App\Models\Catalogs\EstadoInscripcion;
use Illuminate\Support\Facades\DB;
use Exception;

class InscripcionService
{
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

            // BUSCAR REGLA
            $regla = ReglaInscripcion::query()
                ->active()
                ->where('nivel_id', $data['nivel_id'])
                ->where('grado_id', $data['grado_id'])
                ->where(
                    'is_new_admission',
                    $data['is_new_admission']
                )->first();

            if (!$regla) {
                throw new Exception(
                    'No existe una regla de inscripción configurada.'
                );
            }

            // $data['requires_evaluation'] = $regla->requires_evaluation;
            // REQUIERE EVALUACION NUEVA ADMISION
            if (!empty($data['is_new_admission'])) {
                $data['requires_evaluation'] = true;
            } else {
                $data['requires_evaluation'] = $regla->requires_evaluation;
            }

            $data['requires_socioeconomic_study'] = $regla->requires_socioeconomic_study;
            $data['requires_treasury_validation'] = $regla->requires_treasury_validation;
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
            // FECHA
            $data['inscription_date'] = now();
            // COMPLETADO
            $data['is_completed'] = false;
            // AUDITORÍA
            $data['created_by'] = auth()->id();
            $inscripcion = Inscripcion::create($data);

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

            return $inscripcion->load(
                $this->relations()
            );
        });
    }

    public function delete(Inscripcion $inscripcion): void
    {
        $inscripcion->delete();
    }

    public function restore(int $id): ?Inscripcion
    {
        return DB::transaction(function () use ($id) {
            $inscripcion = Inscripcion::withTrashed()->find($id);

            if (!$inscripcion || !$inscripcion->trashed()) {
                return null;
            }

            $inscripcion->restore();

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
}
