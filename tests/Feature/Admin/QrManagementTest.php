<?php

namespace Tests\Feature\Admin;

use App\Models\QrCampaign;
use App\Models\QrRegistrationToken;
use App\Models\SenderistaRegistration;
use App\Models\Tribu;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QrManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_admin_can_create_qr_campaign_and_initial_token(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('Administrador');

        $response = $this->actingAs($admin)->post(route('admin.qr-campaigns.store'), [
            'nombre' => 'Encuentro abril',
            'descripcion' => 'Registro de senderistas para el encuentro.',
            'starts_at' => now()->format('Y-m-d H:i:s'),
            'ends_at' => now()->addDays(10)->format('Y-m-d H:i:s'),
            'activa' => '1',
        ]);

        $campaign = QrCampaign::query()->where('nombre', 'Encuentro abril')->first();

        $this->assertNotNull($campaign);
        $response->assertRedirect(route('admin.qr-campaigns.show', $campaign));
        $response->assertSessionHas('qr_token_url');
        $this->assertDatabaseHas('qr_campaigns', [
            'nombre' => 'Encuentro abril',
            'activa' => true,
            'creado_por' => $admin->id,
        ]);
        $this->assertDatabaseHas('qr_registration_tokens', [
            'qr_campaign_id' => $campaign->id,
            'created_by' => $admin->id,
        ]);

        $token = QrRegistrationToken::query()->where('qr_campaign_id', $campaign->id)->firstOrFail();
        $this->assertNotEmpty($token->raw_token);
        $this->assertStringContainsString('/registro/senderista/', $token->registrationUrl());
    }

    public function test_admin_can_generate_qr_token_for_campaign(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('Administrador');

        $campaign = QrCampaign::query()->create([
            'nombre' => 'Registro general',
            'activa' => true,
            'creado_por' => $admin->id,
        ]);

        $response = $this->actingAs($admin)->post(route('admin.qr-tokens.store'), [
            'qr_campaign_id' => $campaign->id,
            'expires_at' => now()->addDay()->format('Y-m-d H:i:s'),
        ]);

        $response->assertRedirect(route('admin.qr-tokens.index'));
        $response->assertSessionHas('qr_token_url');

        $this->assertDatabaseHas('qr_registration_tokens', [
            'qr_campaign_id' => $campaign->id,
            'created_by' => $admin->id,
        ]);

        $token = QrRegistrationToken::query()->firstOrFail();
        $this->assertSame(64, strlen($token->token_hash));
        $this->assertNotEmpty($token->raw_token);
        $this->assertStringContainsString('/registro/senderista/', $token->registrationUrl());
    }

    public function test_supervisor_can_manage_qr_tokens(): void
    {
        $supervisor = User::factory()->create();
        $supervisor->assignRole('Supervisor');

        $this->actingAs($supervisor)
            ->get(route('admin.qr-tokens.index'))
            ->assertOk();
    }

    public function test_campaign_history_keeps_registration_after_senderista_is_converted(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('Administrador');

        $campaign = QrCampaign::query()->create([
            'nombre' => 'Campaña permanente',
            'activa' => true,
            'creado_por' => $admin->id,
        ]);

        $token = QrRegistrationToken::query()->create([
            'qr_campaign_id' => $campaign->id,
            'token_hash' => hash('sha256', 'example-token'),
            'expires_at' => now()->addDay(),
            'used_at' => now(),
            'created_by' => $admin->id,
        ]);

        $senderista = User::factory()->create([
            'nombre' => 'Miguel',
            'apellido' => 'Campos',
            'numero_legendario' => null,
        ]);
        $senderista->assignRole('Senderista');

        SenderistaRegistration::query()->create([
            'user_id' => $senderista->id,
            'qr_registration_token_id' => $token->id,
            'registered_at' => now(),
        ]);

        $this->actingAs($admin)->post(route('admin.users.convert.store', $senderista), [
            'numero_legendario' => 'L-2026',
            'departamento_id' => null,
        ])->assertRedirect(route('admin.users.show', $senderista));

        $response = $this->actingAs($admin)->get(route('admin.qr-campaigns.show', $campaign));

        $response->assertOk();
        $response->assertSee('Miguel Campos');
        $response->assertSee('Servidor');
        $response->assertSee('100%');
    }

    public function test_admin_can_create_tribe_and_assign_campaign_registration(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('Administrador');

        $campaign = QrCampaign::query()->create([
            'nombre' => 'Campamento',
            'activa' => true,
            'creado_por' => $admin->id,
        ]);

        $token = QrRegistrationToken::query()->create([
            'qr_campaign_id' => $campaign->id,
            'token_hash' => hash('sha256', 'tribe-token'),
            'expires_at' => now()->addDay(),
            'used_at' => now(),
            'created_by' => $admin->id,
        ]);

        $senderista = User::factory()->create([
            'nombre' => 'Ana',
            'apellido' => 'Perez',
        ]);
        $senderista->assignRole('Senderista');

        $registration = SenderistaRegistration::query()->create([
            'user_id' => $senderista->id,
            'qr_registration_token_id' => $token->id,
            'registered_at' => now(),
        ]);

        $this->actingAs($admin)->post(route('admin.qr-campaigns.tribus.store', $campaign), [
            'nombre' => 'Tribu Norte',
            'descripcion' => 'Equipo de bienvenida',
        ])->assertRedirect(route('admin.qr-campaigns.show', $campaign));

        $tribu = Tribu::query()->where('nombre', 'Tribu Norte')->firstOrFail();

        $this->actingAs($admin)->put(route('admin.qr-campaigns.registrations.tribu', [$campaign, $registration]), [
            'tribu_id' => $tribu->id,
        ])->assertRedirect(route('admin.qr-campaigns.show', $campaign));

        $this->assertDatabaseHas('tribu_members', [
            'tribu_id' => $tribu->id,
            'senderista_registration_id' => $registration->id,
            'assigned_by' => $admin->id,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.qr-campaigns.show', $campaign));

        $response->assertOk();
        $response->assertSee('Tribu Norte');
        $response->assertSee('Ana Perez');
        $response->assertSee('1 integrantes');
    }
}
