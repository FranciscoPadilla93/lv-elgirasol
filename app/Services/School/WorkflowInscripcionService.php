<?php

namespace App\Services\School;

use App\Models\School\Inscripcion;
use App\Models\School\ReglaInscripcion;
use App\Models\Catalogs\EstadoInscripcion;
use Illuminate\Support\Facades\DB;
use Exception;

class WorkflowInscripcionService
{
    public function refresh(Inscripcion $inscripcion): Inscripcion
    {
        return DB::transaction(function () use ($inscripcion) {
            $inscripcion->refresh();
            $regla = $this->getRegla($inscripcion);

            // 1. EVALUACIONES
            $evaluationsApproved = $this->areRequiredEvaluationsApproved(
                $inscripcion,
                $regla
            );

            $inscripcion->evaluation_approved = $evaluationsApproved;

            if ($inscripcion->requires_evaluation && !$evaluationsApproved) {
                $this->setEstado(
                    $inscripcion,
                    'evaluation_pending'
                );

                $inscripcion->save();

                return $inscripcion;
            }

            // 2. ESTUDIO SOCIOECONÓMICO
            if ($inscripcion->requires_socioeconomic_study && !$inscripcion->socioeconomic_study_approved) {
                $this->setEstado(
                    $inscripcion,
                    'socioeconomic_pending'
                );

                $inscripcion->save();

                return $inscripcion;
            }

            // 3. TESORERÍA
            if ($inscripcion->requires_treasury_validation && !$inscripcion->treasury_approved) {
                $this->setEstado(
                    $inscripcion,
                    'payment_pending'
                );

                $inscripcion->save();

                return $inscripcion;
            }

            // 4. APROBADA
            if (!$inscripcion->is_completed) {
                $this->setEstado(
                    $inscripcion,
                    'approved'
                );

                $inscripcion->save();

                return $inscripcion;
            }

            // 5. COMPLETADA
            $this->setEstado(
                $inscripcion,
                'completed'
            );

            $inscripcion->save();

            return $inscripcion;
        });
    }

    private function getRegla(Inscripcion $inscripcion): ReglaInscripcion {
        $regla = ReglaInscripcion::query()
            ->active()
            ->where('nivel_id', $inscripcion->nivel_id)
            ->where('grado_id', $inscripcion->grado_id)
            // ->where(
            //     'is_new_admission',
            //     $inscripcion->is_new_admission
            // )
            ->first();

        if (!$regla) {
            throw new Exception(
                'No existe una regla de inscripción configurada.'
            );
        }

        return $regla;
    }

    private function areRequiredEvaluationsApproved(Inscripcion $inscripcion, ReglaInscripcion $regla): bool
    {
        if (!$inscripcion->requires_evaluation) {
            return true;
        }

        $requiredEvaluations = $regla->required_evaluations ?? [];

        if (empty($requiredEvaluations)) {
            return false;
        }

        foreach ($requiredEvaluations as $evaluationCode) {
            $approved = $inscripcion->evaluacionesIniciales()
                ->whereHas(
                    'tipoEvaluacion',
                    function ($query) use ($evaluationCode) {
                        $query->where('code', $evaluationCode);
                    }
                )
                ->where('is_approved', true)
                ->where('status', true)
                ->exists();

            if (!$approved) {
                return false;
            }
        }

        return true;
    }

    private function setEstado(Inscripcion $inscripcion, string $code): void
    {
        $estado = EstadoInscripcion::query()
            ->where('code', $code)
            ->first();

        if (!$estado) {
            throw new Exception(
                "Estado de inscripción no configurado: {$code}"
            );
        }

        $inscripcion->estado_inscripcion_id = $estado->id;
    }
}
