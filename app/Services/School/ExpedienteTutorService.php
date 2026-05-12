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
            // VALIDAR TUTOR PRINCIPAL
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
        $expedienteTutor->delete();
    }

    public function restore(int $id): ?ExpedienteTutor
    {
        return DB::transaction(function () use ($id) {
            $expedienteTutor = ExpedienteTutor::withTrashed()->find($id);

            if (!$expedienteTutor || !$expedienteTutor->trashed()) {
                return null;
            }

            $expedienteTutor->restore();

            return $expedienteTutor->load($this->relations());
        });
    }
}
