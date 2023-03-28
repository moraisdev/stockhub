<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReturnCanceledMail extends Mailable
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
        return $this->subject('O pedido de reembolso do pedido '.$this->return->supplier_order->f_display_id.' foi cancelado pelo lojista')->from('noreply@mawapost.com', config('app.name'))->view('emails.return_canceled');
    }
}
