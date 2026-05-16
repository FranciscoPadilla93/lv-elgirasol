<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class IntranetUserSeeder extends Seeder
{
    public function run(): void
    {
        $adminUserId = User::query()->value('id');

        if (!$adminUserId) {
            return;
        }

        $users = [
            [
                'email' => 'test@demo.com',
                'full_name' => 'User Test',
                'curp' => 'PELJ900101HQTXXX01',
                'password' => Hash::make('Password1'),
                'status' => true,
                'created_by' => $adminUserId,
                'updated_by' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($users as $user) {
            \DB::table('intranet_users')->updateOrInsert(
                ['curp' => $user['curp']],
                $user
            );
        }
    }
}
