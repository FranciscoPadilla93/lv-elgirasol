<?php

namespace App\Services\School;

use App\Models\School\IntranetUser;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class IntranetUserService
{
    public function create(array $data): IntranetUser
    {
        return DB::transaction(function () use ($data) {
            $data['status'] = $data['status'] ?? true;

            $data['password'] = Hash::make(
                $data['password']
            );

            $data['created_by'] = auth()->id();

            $intranetUser = IntranetUser::create($data);

            return $intranetUser->load($this->relations());
        });
    }

    public function update(IntranetUser $intranetUser, array $data): IntranetUser {
        return DB::transaction(function () use ($intranetUser, $data) {
            if (array_key_exists('password', $data) && filled($data['password'])) {
                $data['password'] = Hash::make(
                    $data['password']
                );
            } else {
                unset($data['password']);
            }

            $data['updated_by'] = auth()->id();

            $intranetUser->update($data);

            return $intranetUser
                ->refresh()
                ->load($this->relations());
        });
    }

    public function delete(IntranetUser $intranetUser): void
    {
        DB::transaction(function () use ($intranetUser) {
            $intranetUser->delete();

            $intranetUser->update([
                'status' => false,
                'updated_by' => auth()->id()
            ]);
        });
    }

    public function restore(int $id): ?IntranetUser
    {
        return DB::transaction(function () use ($id) {
            $intranetUser = IntranetUser::withTrashed()->find($id);

            if (!$intranetUser || !$intranetUser->trashed()) {
                return null;
            }

            $intranetUser->restore();

            $intranetUser->update([
                'status' => true,
                'updated_by' => auth()->id()
            ]);

            return $intranetUser->load($this->relations());
        });
    }

    private function relations(): array
    {
        return [
            'createdBy',
            'updatedBy',
        ];
    }
}
