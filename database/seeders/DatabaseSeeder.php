<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
        ]);

        // $this->call(DemoUsersSeeder::class);

        $admin = User::query()->updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'nombre' => 'Admin',
                'apellido' => 'Legendario',
                'rut' => '11111111-1',
                'email' => 'admin@example.com',
                'email_verified_at' => now(),
                'password' => 'password',
                'estado' => 'activo',
                'fecha_inscripcion' => now(),
                'enfermedad' => false,
                'es_pastor' => false,
                'talla' => 'XL',
                'direccion_calle' => 'Sin calle',
                'direccion_numero' => '0',
                'direccion_comuna' => 'Sin comuna',
                'direccion_region' => 'Sin región',
                'direccion_pais' => 'Chile',
                'telefono' => '000000000',
                'nombre_contacto_emergencia' => 'Contacto',
                'parentesco_contacto_emergencia' => 'Otro',
                'telefono_contacto_emergencia' => '000000000',
                'fecha_nacimiento' => '1990-01-01',
            ]
        );

        if (method_exists($admin, 'assignRole')) {
            $admin->assignRole('Administrador');
        }
    }
}
