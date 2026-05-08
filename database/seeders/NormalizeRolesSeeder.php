<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class NormalizeRolesSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $lider = Role::findOrCreate('Líder');
        $servidor = Role::findOrCreate('Servidor');

        foreach (['Lider', 'LIDER'] as $legacyRole) {
            if (! Role::query()->where('name', $legacyRole)->exists()) {
                continue;
            }

            User::query()->role($legacyRole)->each(function (User $user) use ($lider) {
                $user->assignRole($lider);
                $user->removeRole('Lider');
                $user->removeRole('LIDER');
            });

            Role::query()->where('name', $legacyRole)->delete();
        }

        if (Role::query()->where('name', 'Ayudante')->exists()) {
            User::query()->role('Ayudante')->each(function (User $user) use ($servidor) {
                $user->assignRole($servidor);
                $user->removeRole('Ayudante');
            });

            Role::query()->where('name', 'Ayudante')->delete();
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
