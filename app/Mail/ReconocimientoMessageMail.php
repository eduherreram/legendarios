<?php

namespace App\Mail;

use App\Models\Reconocimiento;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReconocimientoMessageMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Reconocimiento $reconocimiento,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->reconocimiento->nombre_reconocimiento,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.reconocimiento-message',
            with: [
                'reconocimiento' => $this->reconocimiento,
            ],
        );
    }
}
