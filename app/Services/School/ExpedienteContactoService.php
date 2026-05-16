<?php

namespace App\Services\School;

use App\Models\School\Expediente;
use App\Models\School\ExpedienteContacto;
use Illuminate\Support\Facades\DB;

class ExpedienteContactoService
{
    private function relations(): array
    {
        return [
            'expediente',
            'parentesco',
            'tipoContacto',
            'createdBy',
            'updatedBy',
        ];
    }

    public function create(Expediente $expediente, array $data): ExpedienteContacto {
        return DB::transaction(function () use ($expediente, $data) {
            $data['expediente_id'] = $expediente->id;
            $data['created_by'] = auth()->id();
            $data['updated_by'] = auth()->id();

            $contacto = ExpedienteContacto::create($data);
            $contacto->refresh();
            return $contacto->load($this->relations());
        });
    }

    public function update(ExpedienteContacto $contacto, array $data): ExpedienteContacto {
        return DB::transaction(function () use ($contacto, $data) {
            $data['updated_by'] = auth()->id();
            $contacto->update($data);
            $contacto->refresh();

            return $contacto->load($this->relations());
        });
    }

    public function delete(ExpedienteContacto $contacto): void {
        DB::transaction(function () use ($contacto) {
            $contacto->update([
                'status' => false,
                'updated_by' => auth()->id()
            ]);

            $contacto->delete();
        });
    }

    public function restore(int $id): ?ExpedienteContacto {
        return DB::transaction(function () use ($id) {
            $contacto = ExpedienteContacto::withTrashed()->find($id);

            if (!$contacto || !$contacto->trashed()) {
                return null;
            }

            $contacto->restore();

            $contacto->update([
                'status' => true,
                'updated_by' => auth()->id()
            ]);
            return $contacto->load($this->relations());
        });
    }
}
