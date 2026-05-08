<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QrCampaign;
use App\Models\SenderistaRegistration;
use App\Models\Tribu;
use App\Models\TribuMember;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TribuController extends Controller
{
    public function store(Request $request, QrCampaign $qrCampaign): RedirectResponse
    {
        $data = $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:255',
                Rule::unique('tribus', 'nombre')->where('qr_campaign_id', $qrCampaign->id),
            ],
            'descripcion' => ['nullable', 'string', 'max:1000'],
        ]);

        $qrCampaign->tribus()->create([
            'nombre' => $data['nombre'],
            'descripcion' => $data['descripcion'] ?? null,
            'created_by' => auth()->id(),
        ]);

        return redirect()
            ->route('admin.qr-campaigns.show', $qrCampaign)
            ->with('success', 'Tribu creada correctamente.');
    }

    public function assign(Request $request, QrCampaign $qrCampaign, SenderistaRegistration $registration): RedirectResponse
    {
        if ((int) $registration->token?->qr_campaign_id !== (int) $qrCampaign->id) {
            abort(404);
        }

        $data = $request->validate([
            'tribu_id' => [
                'nullable',
                'integer',
                Rule::exists('tribus', 'id')->where('qr_campaign_id', $qrCampaign->id),
            ],
        ]);

        if (empty($data['tribu_id'])) {
            $registration->tribuMember()->delete();

            return redirect()
                ->route('admin.qr-campaigns.show', $qrCampaign)
                ->with('success', 'Integrante removido de la tribu.');
        }

        TribuMember::query()->updateOrCreate(
            ['senderista_registration_id' => $registration->id],
            [
                'tribu_id' => $data['tribu_id'],
                'assigned_by' => auth()->id(),
                'assigned_at' => now(),
            ],
        );

        return redirect()
            ->route('admin.qr-campaigns.show', $qrCampaign)
            ->with('success', 'Integrante asignado a la tribu.');
    }

    public function destroy(QrCampaign $qrCampaign, Tribu $tribu): RedirectResponse
    {
        if ((int) $tribu->qr_campaign_id !== (int) $qrCampaign->id) {
            abort(404);
        }

        $tribu->delete();

        return redirect()
            ->route('admin.qr-campaigns.show', $qrCampaign)
            ->with('success', 'Tribu eliminada correctamente.');
    }
}
