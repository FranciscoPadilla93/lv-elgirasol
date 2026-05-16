<?php

namespace App\Services\School;

use App\Models\Catalogs\CicloEscolar;
use Illuminate\Support\Facades\DB;
use Exception;

class CicloEscolarService
{
    public function create(array $data): CicloEscolar
    {
        return DB::transaction(function () use ($data) {
            $data['is_current'] = $data['is_current'] ?? false;
            $data['status'] = $data['status'] ?? true;

            if ($data['is_current']) {
                $this->clearCurrentCycles();
            }

            $cicloEscolar = CicloEscolar::create($data);

            return $cicloEscolar;
        });
    }

    public function update(CicloEscolar $cicloEscolar, array $data): CicloEscolar {
        return DB::transaction(function () use ($cicloEscolar, $data) {
            if (array_key_exists('status', $data) && !$data['status'] && $cicloEscolar->is_current) {
                throw new Exception(
                    'No se puede desactivar el ciclo escolar actual. Primero asigna otro ciclo como actual.'
                );
            }

            if (array_key_exists('is_current', $data) && !$data['is_current'] && $cicloEscolar->is_current) {
                throw new Exception(
                    'No se puede quitar el ciclo escolar actual. Primero asigna otro ciclo como actual.'
                );
            }

            if (array_key_exists('is_current', $data) && $data['is_current']) {
                $this->clearCurrentCycles($cicloEscolar->id);
            }

            $cicloEscolar->update($data);

            return $cicloEscolar->refresh();
        });
    }

    public function delete(CicloEscolar $cicloEscolar): void
    {
        if ($cicloEscolar->is_current) {
            throw new Exception(
                'No se puede eliminar el ciclo escolar actual. Primero asigna otro ciclo como actual.'
            );
        }

        $cicloEscolar->update([
            'status' => false
        ]);

        $cicloEscolar->delete();
    }

    public function restore(int $id): ?CicloEscolar
    {
        return DB::transaction(function () use ($id) {
            $cicloEscolar = CicloEscolar::withTrashed()->find($id);

            if (!$cicloEscolar || !$cicloEscolar->trashed()) {
                return null;
            }

            $cicloEscolar->restore();

            $cicloEscolar->update([
                'status' => true
            ]);

            return $cicloEscolar;
        });
    }

    private function clearCurrentCycles(?int $exceptId = null): void
    {
        CicloEscolar::query()
            ->when($exceptId, function ($query) use ($exceptId) {
                $query->where('id', '!=', $exceptId);
            })
            ->update([
                'is_current' => false,
            ]);
    }
}
