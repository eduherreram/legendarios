<?php

namespace Tests\Feature\Admin;

use App\Mail\ReconocimientoMessageMail;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ReconocimientoManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_admin_can_send_custom_reconocimiento_to_multiple_users(): void
    {
        Mail::fake();

        $admin = User::factory()->create();
        $admin->assignRole('Administrador');

        $first = User::factory()->create([
            'numero_legendario' => 'LM-100',
            'nombre' => 'Jorge',
            'apellido' => 'Calfuquir',
            'telefono' => '+56 9 1234 5678',
        ]);
        $second = User::factory()->create([
            'numero_legendario' => 'LM-101',
            'nombre' => 'Ana',
            'apellido' => 'Rojas',
            'telefono' => '+56 9 8765 4321',
        ]);

        $response = $this->actingAs($admin)->post(route('admin.reconocimientos.store'), [
            'titulo' => 'Servicio destacado',
            'descripcion' => 'Gracias por servir con excelencia.',
            'user_ids' => [$first->id, $second->id],
            'channels' => ['email', 'whatsapp'],
        ]);

        $response->assertRedirect(route('admin.reconocimientos.index'));
        $response->assertSessionHas('whatsapp_links');

        $this->assertDatabaseHas('reconocimientos', [
            'nombre_reconocimiento' => 'Servicio destacado',
            'numero_legendario' => 'LM-100',
            'nombre' => 'Jorge',
            'apellido' => 'Calfuquir',
            'motivo' => 'personalizado',
            'enviar_email' => true,
            'enviar_whatsapp' => true,
            'creado_por' => $admin->id,
        ]);
        $this->assertDatabaseCount('reconocimientos', 2);
        Mail::assertSent(ReconocimientoMessageMail::class, 2);
    }

    public function test_admin_can_send_birthday_greetings_to_todays_birthdays(): void
    {
        Mail::fake();

        $admin = User::factory()->create();
        $admin->assignRole('Administrador');

        $birthdayUser = User::factory()->create([
            'fecha_nacimiento' => now()->subYears(30)->format('Y-m-d'),
            'numero_legendario' => 'LM-200',
        ]);

        $response = $this->actingAs($admin)->post(route('admin.reconocimientos.birthdays.send'), [
            'titulo' => 'Feliz cumpleanos',
            'descripcion' => 'Que tengas un dia lleno de alegria.',
            'user_ids' => [$birthdayUser->id],
            'channels' => ['email'],
        ]);

        $response->assertRedirect(route('admin.reconocimientos.index'));
        $this->assertDatabaseHas('reconocimientos', [
            'user_id' => $birthdayUser->id,
            'motivo' => 'cumpleanos',
            'enviar_email' => true,
        ]);
        Mail::assertSent(ReconocimientoMessageMail::class, 1);
    }

    public function test_supervisor_can_manage_reconocimientos(): void
    {
        Mail::fake();

        $supervisor = User::factory()->create();
        $supervisor->assignRole('Supervisor');
        $destinatario = User::factory()->create();

        $this->actingAs($supervisor)
            ->get(route('admin.reconocimientos.index'))
            ->assertOk();

        $this->actingAs($supervisor)
            ->post(route('admin.reconocimientos.store'), [
                'titulo' => 'Servicio destacado',
                'descripcion' => 'Gracias por servir con excelencia.',
                'user_ids' => [$destinatario->id],
                'channels' => ['email'],
            ])
            ->assertRedirect(route('admin.reconocimientos.index'));

        $this->assertDatabaseHas('reconocimientos', [
            'user_id' => $destinatario->id,
            'motivo' => 'personalizado',
        ]);
    }
}
