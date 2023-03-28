<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactForm extends Mailable
{
    use Queueable, SerializesModels;

    public $name, $email, $messagee;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($name, $email, $messagee)
    {
        $this->name = $name;
        $this->email = $email;
        $this->messagee = $messagee;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.contact_form');
    }
}
