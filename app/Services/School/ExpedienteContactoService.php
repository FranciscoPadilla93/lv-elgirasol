<?php

namespace App\Services\School;

use App\Models\School\Expediente;
use App\Models\School\ExpedienteContacto;
use Illuminate\Support\Facades\DB;

class ExpedienteContactoService
{
    public function create(Expediente $expediente, array $data): ExpedienteContacto {
        return DB::transaction(function () use ($expediente, $data) {
            $data['expediente_id'] = $expediente->id;
            $data['created_by'] = auth()->id();
            $contacto = ExpedienteContacto::create($data);

            return $contacto->load([
                'expediente',
                'parentesco',
                'createdBy',
                'updatedBy',
            ]);
        });
    }

    public function update(ExpedienteContacto $contacto, array $data): ExpedienteContacto {
        return DB::transaction(function () use ($contacto, $data) {
            $data['updated_by'] = auth()->id();
            $contacto->update($data);
            $contacto->refresh();

            return $contacto->load([
                'expediente',
                'parentesco',
                'createdBy',
                'updatedBy',
            ]);
        });
    }

    public function delete(ExpedienteContacto $contacto): void {
        DB::transaction(function () use ($contacto) {
            $contacto->delete();
        });
    }

    public function restore(int $id): ?ExpedienteContacto {
        return DB::transaction(function () use ($id) {
            $contacto = ExpedienteContacto::withTrashed()
                ->find($id);

            if (!$contacto || !$contacto->trashed()) {
                return null;
            }

            $contacto->restore();

            return $contacto->load([
                'expediente',
                'parentesco',
                'createdBy',
                'updatedBy',
            ]);
        });
    }
}
