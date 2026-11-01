<?php

namespace App\Services\Mail;

use App\Jobs\Mail\SendMailJob;
use Illuminate\Mail\Mailable;

class MailService
{
    public function queue(string $recipient, Mailable $mail): void
    {
        SendMailJob::dispatch($recipient, $mail)->afterCommit();
    }
}