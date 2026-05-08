<?php

namespace App\Http\Middleware;

use App\Models\QrRegistrationToken;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateSenderistaRegistrationToken
{
    /**
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $rawToken = (string) $request->route('token');

        if ($rawToken === '') {
            abort(404);
        }

        $token = QrRegistrationToken::query()
            ->where('token_hash', hash('sha256', $rawToken))
            ->first();

        if (! $token) {
            abort(404);
        }

        if ($token->used_at !== null) {
            abort(410);
        }

        if ($token->expires_at !== null && $token->expires_at->isPast()) {
            abort(410);
        }

        if ($token->campaign && ! $token->campaign->activa) {
            abort(410);
        }

        $request->attributes->set('qr_registration_token', $token);

        return $next($request);
    }
}
