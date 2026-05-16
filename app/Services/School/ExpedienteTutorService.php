<?php

namespace App\Services\School;

use App\Models\School\ExpedienteTutor;
use Illuminate\Support\Facades\DB;
use Exception;

class ExpedienteTutorService
{
    private function relations(): array
    {
        return [
            'expediente',
            'tutor',
            'parentesco',
        ];
    }

    public function create(array $data): ExpedienteTutor
    {
        return DB::transaction(function () use ($data) {
            $exists = ExpedienteTutor::query()
                ->where('expediente_id', $data['expediente_id'])
                ->where('tutor_id', $data['tutor_id'])
                ->whereNull('deleted_at')
                ->exists();

            if ($exists) {
                throw new Exception('El tutor ya está relacionado con este expediente.');
            }

            // VALIDAR TUTOR PRINCIPAL
            if (!empty($data['is_primary_contact'])) {
                $existsPrimary = ExpedienteTutor::query()
                    ->where('expediente_id', $data['expediente_id'])
                    ->where('is_primary_contact', true)
                    ->whereNull('deleted_at')
                    ->exists();

                if ($existsPrimary) {

                    throw new Exception(
                        'El expediente ya tiene un tutor principal asignado.'
                    );
                }
            }
            // AUDITORÍA
            $data['created_by'] = auth()->id();
            $expedienteTutor = ExpedienteTutor::create($data);

            return $expedienteTutor->load($this->relations());
        });
    }

    public function update(ExpedienteTutor $expedienteTutor,array $data): ExpedienteTutor {
        return DB::transaction(function () use ($expedienteTutor,$data) {
            // VALIDAR TUTOR PRINCIPAL Y DUPLICIDAD
            $expedienteId = $data['expediente_id'] ?? $expedienteTutor->expediente_id;
            $tutorId = $data['tutor_id'] ?? $expedienteTutor->tutor_id;

            $exists = ExpedienteTutor::query()
                ->where('expediente_id', $expedienteId)
                ->where('tutor_id', $tutorId)
                ->where('id', '!=', $expedienteTutor->id)
                ->whereNull('deleted_at')
                ->exists();

            if ($exists) {
                throw new Exception('El tutor ya está relacionado con este expediente.');
            }

            if (!empty($data['is_primary_contact'])) {
                $existsPrimary = ExpedienteTutor::query()
                    ->where('expediente_id', $data['expediente_id'] ?? $expedienteTutor->expediente_id)
                    ->where('is_primary_contact', true)
                    ->where('id', '!=', $expedienteTutor->id)
                    ->whereNull('deleted_at')
                    ->exists();

                if ($existsPrimary) {
                    throw new Exception(
                        'El expediente ya tiene un tutor principal asignado.'
                    );
                }
            }
            // AUDITORÍA
            $data['updated_by'] = auth()->id();
            $expedienteTutor->update($data);
            $expedienteTutor->refresh();

            return $expedienteTutor->load($this->relations());
        });
    }

    public function delete(ExpedienteTutor $expedienteTutor): void {
        DB::transaction(function () use ($expedienteTutor) {
            $expedienteTutor->delete();
            $expedienteTutor->update([
                'status' => false,
                'updated_by' => auth()->id()
            ]);
        });
    }

    public function restore(int $id): ?ExpedienteTutor
    {
        return DB::transaction(function () use ($id) {
            $expedienteTutor = ExpedienteTutor::withTrashed()->find($id);

            if (!$expedienteTutor || !$expedienteTutor->trashed()) {
                return null;
            }

            $expedienteTutor->restore();
            $expedienteTutor->update([
                'status' => true,
                'updated_by' => auth()->id()
            ]);
            return $expedienteTutor->load($this->relations());
        });
    }
}
