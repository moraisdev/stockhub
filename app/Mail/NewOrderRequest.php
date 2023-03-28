<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewOrderRequest extends Mailable
{
    use Queueable, SerializesModels;

    public $return;

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
        return $this->subject('Nova Solicitação de Pedidos')->from('noreply@mawapost.com', config('app.name'))->view('emails.new_order_request');
    }
}
