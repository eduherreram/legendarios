<?php

namespace Tests\Feature\Admin;

use App\Models\Departamento;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinanzasManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_encargado_cannot_create_finanzas_movement(): void
    {
        $encargado = User::factory()->create();
        $encargado->assignRole('Encargado');

        $departamento = Departamento::query()->create([
            'nombre' => 'Logistica',
            'encargado_id' => $encargado->id,
            'lider_id' => null,
            'supervisor_id' => null,
        ]);

        $encargado->forceFill(['departamento_id' => $departamento->id])->save();

        $response = $this->actingAs($encargado)->post(route('admin.finanzas.store'), [
            'departamento_id' => $departamento->id,
            'tipo' => 'ingreso',
            'monto' => 120000,
            'descripcion' => 'Aporte especial',
            'fecha' => '2026-04-26',
        ]);

        $response->assertForbidden();
        $this->assertDatabaseCount('finanzas_movimientos', 0);
    }

    public function test_finanzas_role_can_create_movements_for_any_department(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Finanzas');

        $departamento = Departamento::query()->create([
            'nombre' => 'Comunicaciones',
            'encargado_id' => null,
            'lider_id' => null,
            'supervisor_id' => null,
        ]);
        $user->forceFill(['departamento_id' => $departamento->id])->save();

        $response = $this->actingAs($user)->post(route('admin.finanzas.store'), [
            'departamento_id' => $departamento->id,
            'tipo' => 'egreso',
            'monto' => 1000,
            'descripcion' => 'Compra menor',
            'fecha' => '2026-04-26',
        ]);

        $response->assertRedirect(route('admin.finanzas.index'));
        $this->assertDatabaseHas('finanzas_movimientos', [
            'departamento_id' => $departamento->id,
            'tipo' => 'egreso',
            'monto' => 1000,
            'descripcion' => 'Compra menor',
        ]);
    }
}
