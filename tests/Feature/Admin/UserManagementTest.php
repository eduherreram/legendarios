<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_admin_can_create_user_and_assign_role(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('Administrador');

        $payload = [
            'numero_legendario' => 'L-9001',
            'nombre' => 'Carlos',
            'apellido' => 'Ruiz',
            'rut' => '12345678-9',
            'fecha_nacimiento' => '1991-01-10',
            'enfermedad' => false,
            'talla' => 'L',
            'iglesia' => 'Iglesia Central',
            'es_pastor' => false,
            'direccion_calle' => 'Siempre Viva',
            'direccion_numero' => '742',
            'direccion_comuna' => 'Santiago',
            'direccion_region' => 'Metropolitana',
            'direccion_pais' => 'Chile',
            'telefono' => '+56911111111',
            'email' => 'carlos.ruiz@example.com',
            'nombre_contacto_emergencia' => 'Ana Ruiz',
            'parentesco_contacto_emergencia' => 'Hermana',
            'telefono_contacto_emergencia' => '+56922222222',
            'estado' => 'activo',
            'role' => 'Senderista',
        ];

        $response = $this->actingAs($admin)->post(route('admin.users.store'), $payload);

        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', [
            'email' => 'carlos.ruiz@example.com',
            'nombre' => 'Carlos',
            'apellido' => 'Ruiz',
        ]);

        $created = User::query()->where('email', 'carlos.ruiz@example.com')->firstOrFail();
        $this->assertTrue($created->hasRole('Senderista'));
    }

    public function test_supervisor_cannot_create_users(): void
    {
        $supervisor = User::factory()->create();
        $supervisor->assignRole('Supervisor');

        $response = $this->actingAs($supervisor)->get(route('admin.users.create'));

        $response->assertForbidden();
    }

    public function test_supervisor_can_view_admin_modules(): void
    {
        $supervisor = User::factory()->create();
        $supervisor->assignRole('Supervisor');

        $response = $this->actingAs($supervisor)->get(route('admin.users.index'));
        $response->assertOk();

        $response = $this->actingAs($supervisor)->get(route('admin.accesos.index'));
        $response->assertOk();

        $response = $this->actingAs($supervisor)->get(route('admin.dashboard'));
        $response->assertOk();
    }

    public function test_admin_can_convert_senderista_to_servidor(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('Administrador');

        $senderista = User::factory()->create([
            'numero_legendario' => null,
            'departamento_id' => null,
        ]);
        $senderista->assignRole('Senderista');

        $payload = [
            'numero_legendario' => 'L-777',
            'departamento_id' => null,
        ];

        $response = $this->actingAs($admin)->post(route('admin.users.convert.store', $senderista), $payload);

        $response->assertRedirect(route('admin.users.show', $senderista));

        $senderista->refresh();
        $this->assertSame('L-777', $senderista->numero_legendario);
        $this->assertTrue($senderista->hasRole('Servidor'));
        $this->assertFalse($senderista->hasRole('Senderista'));
    }
}
