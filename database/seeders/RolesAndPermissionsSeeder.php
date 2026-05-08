<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach ([
            'Administrador',
            'Supervisor',
            'Encargado',
            'Líder',
            'Finanzas',
            'Servidor',
            'Senderista',
        ] as $roleName) {
            Role::findOrCreate($roleName);
        }
    }
}
