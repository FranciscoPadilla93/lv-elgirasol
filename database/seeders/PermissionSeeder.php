<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'read' => 'Read',
            'create' => 'Create',
            'update' => 'Update',
            'delete' => 'Delete',
            'export' => 'Export',
            'import' => 'Import',
            'assign_permissions' => 'Assign Permissions',
        ];

        foreach ($permissions as $code => $name) {
            $permission = Permission::withTrashed()->updateOrCreate(
                ['code' => $code],
                ['name' => $name],
            );

            $permission->restore();
        }
    }
}
