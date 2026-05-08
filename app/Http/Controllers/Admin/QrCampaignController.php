<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreQrCampaignRequest;
use App\Http\Requests\Admin\UpdateQrCampaignRequest;
use App\Models\QrCampaign;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class QrCampaignController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));

        $campaigns = QrCampaign::query()
            ->withCount([
                'tokens',
                'tokens as tokens_usados_count' => fn ($query) => $query->whereNotNull('used_at'),
                'registrations',
            ])
            ->when($q !== '', function ($query) use ($q) {
                $query->where('nombre', 'like', "%{$q}%");
            })
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.qr-campaigns.index', [
            'campaigns' => $campaigns,
            'q' => $q,
        ]);
    }

    public function show(QrCampaign $qrCampaign): View
    {
        $registrations = $qrCampaign->registrations()
            ->with(['user.roles', 'user.departamento', 'token', 'tribuMember.tribu'])
            ->orderByDesc('registered_at')
            ->paginate(20);

        $tokens = $qrCampaign->tokens()
            ->with('usedByUser:id,nombre,apellido,email')
            ->orderByDesc('id')
            ->get();

        $tribus = $qrCampaign->tribus()
            ->withCount('members')
            ->orderBy('nombre')
            ->get();

        $totalTokens = $qrCampaign->tokens()->count();
        $usedTokens = $qrCampaign->tokens()->whereNotNull('used_at')->count();
        $expiredTokens = $qrCampaign->tokens()
            ->whereNull('used_at')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', now())
            ->count();
        $availableTokens = $qrCampaign->tokens()
            ->whereNull('used_at')
            ->where(fn ($query) => $query->whereNull('expires_at')->orWhere('expires_at', '>', now()))
            ->count();
        $convertedCount = $qrCampaign->registrations()
            ->whereHas('user', fn ($query) => $query->role('Servidor'))
            ->count();
        $assignedToTribuCount = $qrCampaign->registrations()
            ->whereHas('tribuMember')
            ->count();
        $registrationsCount = $qrCampaign->registrations()->count();

        return view('admin.qr-campaigns.show', [
            'campaign' => $qrCampaign,
            'registrations' => $registrations,
            'tokens' => $tokens,
            'tribus' => $tribus,
            'metrics' => [
                'tokens_total' => $totalTokens,
                'tokens_usados' => $usedTokens,
                'tokens_disponibles' => $availableTokens,
                'tokens_expirados' => $expiredTokens,
                'registrados' => $registrationsCount,
                'convertidos' => $convertedCount,
                'conversion_pct' => $registrationsCount > 0
                    ? (int) round(($convertedCount / $registrationsCount) * 100)
                    : 0,
                'tribus' => $tribus->count(),
                'con_tribu' => $assignedToTribuCount,
                'con_tribu_pct' => $registrationsCount > 0
                    ? (int) round(($assignedToTribuCount / $registrationsCount) * 100)
                    : 0,
            ],
        ]);
    }

    public function create(): View
    {
        return view('admin.qr-campaigns.form', [
            'campaign' => new QrCampaign(),
        ]);
    }

    public function store(StoreQrCampaignRequest $request): RedirectResponse
    {
        [$campaign, $registrationUrl] = DB::transaction(function () use ($request) {
            $campaign = QrCampaign::query()->create($this->payload($request->validated()));

            $rawToken = Str::random(64);
            $campaign->tokens()->create([
                'token_hash' => hash('sha256', $rawToken),
                'raw_token' => $rawToken,
                'expires_at' => $campaign->ends_at ?: now()->addDays(7),
                'used_at' => null,
                'used_by_user_id' => null,
                'created_by' => auth()->id(),
            ]);

            return [$campaign, route('senderista.register.create', ['token' => $rawToken])];
        });

        return redirect()
            ->route('admin.qr-campaigns.show', $campaign)
            ->with('success', 'Campana QR creada correctamente. Se genero un token inicial.')
            ->with('qr_token_url', $registrationUrl);
    }

    public function edit(QrCampaign $qrCampaign): View
    {
        return view('admin.qr-campaigns.form', [
            'campaign' => $qrCampaign,
        ]);
    }

    public function update(UpdateQrCampaignRequest $request, QrCampaign $qrCampaign): RedirectResponse
    {
        $qrCampaign->forceFill($this->payload($request->validated()))->save();

        return redirect()->route('admin.qr-campaigns.index')->with('success', 'Campaña QR actualizada correctamente.');
    }

    public function destroy(QrCampaign $qrCampaign): RedirectResponse
    {
        $qrCampaign->delete();

        return redirect()->route('admin.qr-campaigns.index')->with('success', 'Campaña QR eliminada correctamente.');
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function payload(array $data): array
    {
        return [
            'nombre' => $data['nombre'],
            'descripcion' => $data['descripcion'] ?? null,
            'starts_at' => $data['starts_at'] ?? null,
            'ends_at' => $data['ends_at'] ?? null,
            'activa' => (bool) ($data['activa'] ?? false),
            'creado_por' => auth()->id(),
        ];
    }
}
