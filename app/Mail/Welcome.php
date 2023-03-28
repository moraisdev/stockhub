<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\EmailTemplate;

class Welcome extends Mailable
{
    use Queueable, SerializesModels;

    public $return;
    public $message;
    

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($return)
    {
        $this->return = $return;
        
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $emails = EmailTemplate::find(2);
        $message = $emails->template; 
        return $this->subject('Bem-vindo')->from('noreply@mawapost.com', config('app.name'))->view('emails.welcome')->with('msg', $message);
    }
}
