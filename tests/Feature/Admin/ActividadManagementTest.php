<?php

namespace Tests\Feature\Admin;

use App\Models\Actividad;
use App\Models\Departamento;
use App\Models\DepartamentoActividad;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActividadManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_admin_can_create_activity_and_generate_registration_link_without_attendance(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('Administrador');

        $departamento = Departamento::query()->create([
            'nombre' => 'Intercesion',
            'supervisor_id' => null,
            'lider_id' => null,
            'encargado_id' => null,
        ]);

        $activeUser = User::factory()->create([
            'departamento_id' => $departamento->id,
            'estado' => 'activo',
            'numero_legendario' => 'LEG-001',
        ]);
        $inactiveUser = User::factory()->create([
            'departamento_id' => $departamento->id,
            'estado' => 'inactivo',
            'numero_legendario' => 'LEG-002',
        ]);

        $response = $this->actingAs($admin)->post(route('admin.actividades.store'), [
            'nombre' => 'Reunion semanal',
            'fecha' => '2026-04-30',
        ]);

        $actividad = Actividad::query()->where('nombre', 'Reunion semanal')->firstOrFail();

        $response
            ->assertRedirect(route('admin.actividades.show', $actividad))
            ->assertSessionHas('actividad_registration_url');

        $depActividad = DepartamentoActividad::query()
            ->where('actividad_id', $actividad->id)
            ->where('departamento_id', $departamento->id)
            ->firstOrFail();

        $this->assertNotNull($actividad->raw_registration_token);

        $this->assertDatabaseHas('departamento_actividades', [
            'id' => $depActividad->id,
            'pago_monto_total' => 0,
            'pago_monto_pagado' => 0,
        ]);

        $this->assertDatabaseMissing('asistencias', [
            'departamento_actividad_id' => $depActividad->id,
            'user_id' => $activeUser->id,
        ]);

        $this->assertDatabaseMissing('asistencias', [
            'departamento_actividad_id' => $depActividad->id,
            'user_id' => $inactiveUser->id,
        ]);
    }

    public function test_public_activity_registration_creates_commitment_and_attendance(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('Administrador');

        $departamento = Departamento::query()->create([
            'nombre' => 'Logistica',
            'supervisor_id' => null,
            'lider_id' => null,
            'encargado_id' => null,
        ]);

        $servidor = User::factory()->create([
            'departamento_id' => $departamento->id,
            'estado' => 'activo',
            'numero_legendario' => 'LEG-100',
        ]);
        $servidor->assignRole('Servidor');

        $this->actingAs($admin)->post(route('admin.actividades.store'), [
            'nombre' => 'Servicio especial',
            'fecha' => '2026-05-12',
            'precio' => 5000,
        ])->assertRedirect();

        $actividad = Actividad::query()->where('nombre', 'Servicio especial')->firstOrFail();

        $response = $this->post(route('actividad.register.store', $actividad->raw_registration_token), [
            'numero_legendario' => 'LEG-100',
        ]);

        $response->assertRedirect(route('actividad.register.success'));

        $depActividad = DepartamentoActividad::query()
            ->where('actividad_id', $actividad->id)
            ->where('departamento_id', $departamento->id)
            ->firstOrFail();

        $this->assertDatabaseHas('actividad_compromisos', [
            'actividad_id' => $actividad->id,
            'departamento_actividad_id' => $depActividad->id,
            'user_id' => $servidor->id,
        ]);

        $this->assertDatabaseHas('asistencias', [
            'departamento_actividad_id' => $depActividad->id,
            'user_id' => $servidor->id,
            'estado' => 'ausente',
            'pago_estado' => 'pendiente',
        ]);

        $this->assertDatabaseHas('departamento_actividades', [
            'id' => $depActividad->id,
            'pago_estado' => 'pendiente',
            'pago_monto_total' => 5000,
            'pago_monto_pagado' => 0,
        ]);
    }
}
