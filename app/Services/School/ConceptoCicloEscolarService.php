<?php

namespace App\Services\School;

use App\Models\School\ConceptoCicloEscolar;
use Illuminate\Support\Facades\DB;

class ConceptoCicloEscolarService
{
    protected array $relations = [
        'concepto',
        'cicloEscolar',
    ];

    public function getAll(array $filters)
    {
        $query = ConceptoCicloEscolar::query()
            ->with($this->relations);

        if (isset($filters['with_trashed']) && $filters['with_trashed']) {
            $query->withTrashed();
        }

        if (isset($filters['only_trashed']) && $filters['only_trashed']) {
            $query->onlyTrashed();
        }

        if (!empty($filters['concepto_id'])) {
            $query->where('concepto_id', $filters['concepto_id']);
        }

        if (!empty($filters['ciclo_escolar_id'])) {
            $query->where('ciclo_escolar_id', $filters['ciclo_escolar_id']);
        }

        if (isset($filters['status']) && $filters['status'] !== '') {
            $query->where(
                'status',
                filter_var($filters['status'], FILTER_VALIDATE_BOOLEAN)
            );
        }
        if (isset($filters['has_late_fee']) && $filters['has_late_fee'] !== '') {
            $query->where(
                'has_late_fee',
                filter_var($filters['has_late_fee'], FILTER_VALIDATE_BOOLEAN)
            );
        }
        if (!empty($filters['search'])) {
            $search = $filters['search'];

            $query->whereHas('concepto', function ($q) use ($search) {
                $q->where('code', 'LIKE', "%{$search}%")
                    ->orWhere('name', 'LIKE', "%{$search}%");
            });
        }
        $sortBy = $filters['sort_by'] ?? 'id';
        $sortDirection = $filters['sort_direction'] ?? 'desc';

        $allowedSorts = [
            'id',
            'concepto_id',
            'ciclo_escolar_id',
            'price',
            'start_date',
            'due_date',
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

    public function find(int $id): ConceptoCicloEscolar
    {
        return ConceptoCicloEscolar::withTrashed()
            ->with($this->relations)
            ->findOrFail($id);
    }

    public function create(array $data): ConceptoCicloEscolar
    {
        return DB::transaction(function () use ($data) {
            $data['status'] = $data['status'] ?? true;
            $data['has_late_fee'] = $data['has_late_fee'] ?? false;
            $data['created_by'] = auth()->id();

            $conceptoCicloEscolar = ConceptoCicloEscolar::create($data);

            return $conceptoCicloEscolar->load($this->relations);
        });
    }

    public function update(ConceptoCicloEscolar $conceptoCicloEscolar, array $data): ConceptoCicloEscolar {
        return DB::transaction(function () use ($conceptoCicloEscolar, $data) {
            $data['updated_by'] = auth()->id();
            $conceptoCicloEscolar->update($data);

            return $conceptoCicloEscolar
                ->refresh()
                ->load($this->relations);
        });
    }

    public function delete(ConceptoCicloEscolar $conceptoCicloEscolar): void
    {
        DB::transaction(function () use ($conceptoCicloEscolar) {
            $conceptoCicloEscolar->delete();

            $conceptoCicloEscolar->update([
                'status' => false,
                'updated_by' => auth()->id()
            ]);
        });
    }

    public function restore(int $id): ConceptoCicloEscolar
    {
        return DB::transaction(function () use ($id) {
            $conceptoCicloEscolar = ConceptoCicloEscolar::onlyTrashed()->findOrFail($id);
            $conceptoCicloEscolar->restore();

            $conceptoCicloEscolar->update([
                'status' => true,
                'updated_by' => auth()->id()
            ]);

            return $conceptoCicloEscolar
                ->refresh()
                ->load($this->relations);
        });
    }
}
