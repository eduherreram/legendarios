<?php

namespace Database\Seeders;

use App\Models\Departamento;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoUsersSeeder extends Seeder
{
    public function run(): void
    {
        $departamentos = Departamento::query()->orderBy('nombre')->get();

        if ($departamentos->isEmpty()) {
            $departamentos = collect([
                Departamento::query()->create(['nombre' => 'Logística']),
                Departamento::query()->create(['nombre' => 'Cocina']),
                Departamento::query()->create(['nombre' => 'Asistencia']),
            ]);
        }

        $roles = ['Supervisor', 'Encargado', 'Líder', 'Servidor', 'Senderista'];

        User::factory()
            ->count(10)
            ->create([
                'estado' => 'activo',
            ])
            ->each(function (User $user) use ($departamentos, $roles) {
                $user->departamento_id = $departamentos->random()->id;
                $user->save();

                if (method_exists($user, 'assignRole')) {
                    $user->assignRole($roles[array_rand($roles)]);
                }
            });
    }
}
