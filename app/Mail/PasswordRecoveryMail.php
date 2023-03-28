<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordRecoveryMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user, $url;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $url)
    {
        $this->user = $user;
        $this->url = $url;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Recuperação de Senha no '.config('app.name'))->from('noreply@mawapost.com', config('app.name'))->view('emails.password_recovery');
    }
}
