<?php

namespace Database\Seeders;

use App\Models\Departamento;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class TestOrganizationSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (['Supervisor', 'Encargado', 'Líder', 'Servidor'] as $roleName) {
            Role::findOrCreate($roleName);
        }

        $departamentos = [
            'Guias',
            'Avanzada',
            'Logistica',
            'Cocina',
            'Seguridad',
            'Voces',
            'Media',
        ];

        foreach ($departamentos as $index => $nombreDepartamento) {
            $departamento = Departamento::query()->firstOrCreate([
                'nombre' => $nombreDepartamento,
            ]);

            $slug = str($nombreDepartamento)->ascii()->lower()->slug('-')->toString();
            $baseRut = 30000000 + ($index * 100);

            $supervisor = $this->userForDepartment($departamento, $slug, 'Supervisor', $baseRut + 1);
            $lider = $this->userForDepartment($departamento, $slug, 'Líder', $baseRut + 2);
            $encargado = $this->userForDepartment($departamento, $slug, 'Encargado', $baseRut + 3);

            $this->userForDepartment($departamento, $slug, 'Servidor 1', $baseRut + 4, 'Servidor');
            $this->userForDepartment($departamento, $slug, 'Servidor 2', $baseRut + 5, 'Servidor');

            $departamento->forceFill([
                'supervisor_id' => $supervisor->id,
                'lider_id' => $lider->id,
                'encargado_id' => $encargado->id,
            ])->save();
        }
    }

    private function userForDepartment(
        Departamento $departamento,
        string $departmentSlug,
        string $label,
        int $rutNumber,
        ?string $role = null,
    ): User {
        $role ??= $label;
        $labelSlug = str($label)->ascii()->lower()->slug('-')->toString();

        $user = User::query()->updateOrCreate(
            ['email' => "{$departmentSlug}.{$labelSlug}@example.test"],
            [
                'numero_legendario' => strtoupper(substr($departmentSlug, 0, 3)).'-'.$rutNumber,
                'nombre' => $label,
                'apellido' => $departamento->nombre,
                'rut' => $rutNumber.'-'.($rutNumber % 10),
                'fecha_nacimiento' => '1990-01-01',
                'enfermedad' => false,
                'talla' => 'L',
                'iglesia' => 'Iglesia Central',
                'es_pastor' => false,
                'direccion_calle' => 'Calle Demo',
                'direccion_numero' => (string) ($rutNumber % 1000),
                'direccion_comuna' => 'Santiago',
                'direccion_region' => 'Metropolitana',
                'direccion_pais' => 'Chile',
                'telefono' => '+569'.substr((string) $rutNumber, -8),
                'password' => 'password',
                'nombre_contacto_emergencia' => 'Contacto Demo',
                'parentesco_contacto_emergencia' => 'Familiar',
                'telefono_contacto_emergencia' => '+56900000000',
                'estado' => 'activo',
                'fecha_inscripcion' => now(),
                'departamento_id' => $departamento->id,
            ],
        );

        $user->syncRoles([$role]);

        return $user;
    }
}
