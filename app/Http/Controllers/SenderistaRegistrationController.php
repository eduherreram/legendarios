<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSenderistaRegistrationRequest;
use App\Models\QrRegistrationToken;
use App\Services\SenderistaRegistrationService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SenderistaRegistrationController extends Controller
{
    public function __construct(
        private readonly SenderistaRegistrationService $service,
    ) {
    }

    public function create(Request $request): View
    {
        /** @var QrRegistrationToken $token */
        $token = $request->attributes->get('qr_registration_token');

        return view('registro.senderista', [
            'campaign' => $token->campaign,
        ]);
    }

    public function store(StoreSenderistaRegistrationRequest $request): RedirectResponse
    {
        /** @var QrRegistrationToken $token */
        $token = $request->attributes->get('qr_registration_token');

        $this->service->register($token, $request->validated());

        return redirect()->route('senderista.register.success')->with('success', 'Registro enviado correctamente.');
    }

    public function success(): View
    {
        return view('registro.senderista-success');
    }
}
