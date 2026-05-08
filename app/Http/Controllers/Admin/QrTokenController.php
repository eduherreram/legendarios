<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreQrTokenRequest;
use App\Models\QrCampaign;
use App\Models\QrRegistrationToken;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class QrTokenController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->string('status')->toString();

        $tokens = QrRegistrationToken::query()
            ->with(['campaign', 'usedByUser'])
            ->when($status === 'available', fn ($query) => $query->whereNull('used_at')->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now())))
            ->when($status === 'used', fn ($query) => $query->whereNotNull('used_at'))
            ->when($status === 'expired', fn ($query) => $query->whereNull('used_at')->whereNotNull('expires_at')->where('expires_at', '<=', now()))
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.qr-tokens.index', [
            'tokens' => $tokens,
            'status' => $status,
            'latestUrl' => session('qr_token_url'),
        ]);
    }

    public function create(Request $request): View
    {
        return view('admin.qr-tokens.form', [
            'campaigns' => QrCampaign::query()->orderByDesc('activa')->orderBy('nombre')->get(),
            'selectedCampaignId' => $request->integer('qr_campaign_id') ?: null,
        ]);
    }

    public function store(StoreQrTokenRequest $request): RedirectResponse
    {
        $rawToken = Str::random(64);
        $data = $request->validated();

        QrRegistrationToken::query()->create([
            'qr_campaign_id' => $data['qr_campaign_id'] ?? null,
            'token_hash' => hash('sha256', $rawToken),
            'raw_token' => $rawToken,
            'expires_at' => $data['expires_at'] ?? now()->addDays(7),
            'used_at' => null,
            'used_by_user_id' => null,
            'created_by' => auth()->id(),
        ]);

        return redirect()
            ->route('admin.qr-tokens.index')
            ->with('success', 'Token QR creado correctamente.')
            ->with('qr_token_url', route('senderista.register.create', ['token' => $rawToken]));
    }
}
