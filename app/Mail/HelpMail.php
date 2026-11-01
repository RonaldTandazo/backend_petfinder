<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class HelpMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        protected array $data,
        protected string $type
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Nueva solicitud de {$this->type} - PetFinder"
        );
    }

    public function content(): Content
    {   
        return new Content(
            view: 'emails.help.created',
            with: [
                'data' => $this->data,
                'type' => $this->type,
            ]
        );
    }
}