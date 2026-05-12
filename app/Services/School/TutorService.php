<?php

namespace App\Services\School;

use App\Models\School\Tutor;
use Illuminate\Support\Facades\DB;

class TutorService
{
    private function relations(): array
    {
        return [
            'genero',
            'user',
        ];
    }

    public function create(array $data): Tutor
    {
        return DB::transaction(function () use ($data) {
            // AUDITORÍA
            $data['created_by'] = auth()->id();
            $tutor = Tutor::create($data);

            return $tutor->load($this->relations());
        });
    }

    public function update(Tutor $tutor, array $data): Tutor
    {
        return DB::transaction(function () use ($tutor, $data) {
            // AUDITORÍA
            $data['updated_by'] = auth()->id();
            $tutor->update($data);
            $tutor->refresh();

            return $tutor->load($this->relations());
        });
    }

    public function delete(Tutor $tutor): void
    {
        $tutor->delete();
    }

    public function restore(int $id): ?Tutor
    {
        return DB::transaction(function () use ($id) {
            $tutor = Tutor::withTrashed()->find($id);

            if (!$tutor || !$tutor->trashed()) {
                return null;
            }

            $tutor->restore();

            return $tutor->load($this->relations());
        });
    }
}
