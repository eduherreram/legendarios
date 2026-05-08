<?php

namespace App\Console\Commands;

use App\Models\QrRegistrationToken;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class GenerateSenderistaQrToken extends Command
{
    protected $signature = 'senderista:token {--expires= : Minutes until expiration (default 10080 = 7 days)}';

    protected $description = 'Genera un token para registro público de Senderista y muestra la URL final';

    public function handle(): int
    {
        $rawToken = Str::random(64);
        $minutes = (int) ($this->option('expires') ?: 10080);

        $token = QrRegistrationToken::query()->create([
            'qr_campaign_id' => null,
            'token_hash' => hash('sha256', $rawToken),
            'raw_token' => $rawToken,
            'expires_at' => now()->addMinutes($minutes),
            'used_at' => null,
            'used_by_user_id' => null,
            'created_by' => null,
        ]);

        $url = url('/registro/senderista/'.$rawToken);

        $this->info('Token ID: '.$token->id);
        $this->info('URL: '.$url);
        $this->line('Raw token (para generar QR): '.$rawToken);

        return self::SUCCESS;
    }
}
