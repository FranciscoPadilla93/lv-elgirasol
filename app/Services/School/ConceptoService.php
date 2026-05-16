<?php

namespace App\Services\School;

use App\Models\School\Concepto;
use Illuminate\Support\Facades\DB;

class ConceptoService
{
    public function getAll(array $filters)
    {
        $query = Concepto::query();

        if (isset($filters['with_trashed']) && $filters['with_trashed']) {
            $query->withTrashed();
        }

        if (isset($filters['only_trashed']) && $filters['only_trashed']) {
            $query->onlyTrashed();
        }

        if (isset($filters['status']) && $filters['status'] !== '') {
            $query->where(
                'status',
                filter_var($filters['status'], FILTER_VALIDATE_BOOLEAN)
            );
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];

            $query->where(function ($q) use ($search) {
                $q->where('code', 'LIKE', "%{$search}%")
                    ->orWhere('name', 'LIKE', "%{$search}%")
                    ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        $sortBy = $filters['sort_by'] ?? 'id';
        $sortDirection = $filters['sort_direction'] ?? 'desc';

        $allowedSorts = [
            'id',
            'code',
            'name',
            'status',
            'created_at',
        ];

        if (!in_array($sortBy, $allowedSorts)) {
            $sortBy = 'id';
        }

        if (!in_array(strtolower($sortDirection), ['asc', 'desc'])) {
            $sortDirection = 'desc';
        }

        return $query
            ->orderBy($sortBy, $sortDirection)
            ->paginate($filters['per_page'] ?? 15);
    }

    public function find(int $id): Concepto
    {
        return Concepto::withTrashed()->findOrFail($id);
    }

    public function create(array $data): Concepto
    {
        return DB::transaction(function () use ($data) {
            $data['status'] = $data['status'] ?? true;
            $data['created_by'] = auth()->id();

            return Concepto::create($data);
        });
    }

    public function update(Concepto $concepto, array $data): Concepto
    {
        return DB::transaction(function () use ($concepto, $data) {
            $data['updated_by'] = auth()->id();

            $concepto->update($data);

            return $concepto->refresh();
        });
    }

    public function delete(Concepto $concepto): void
    {
        DB::transaction(function () use ($concepto) {
            $concepto->delete();

            $concepto->update([
                'status' => false,
                'updated_by' => auth()->id()
            ]);
        });
    }

    public function restore(int $id): Concepto
    {
        return DB::transaction(function () use ($id) {
            $concepto = Concepto::onlyTrashed()->findOrFail($id);
            $concepto->restore();

            $concepto->update([
                'status' => true,
                'updated_by' => auth()->id()
            ]);

            return $concepto->refresh();
        });
    }
}
