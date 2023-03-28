<?php

namespace App\Mail;

use App\Models\Orders;
use App\Models\Receipts;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReceiptMail extends Mailable
{
    use Queueable, SerializesModels;

    public $order, $receipt;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Orders $order, Receipts $receipt)
    {
        $this->order = $order;
        $this->receipt = $receipt;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->order->shop->name.' - Nota fiscal do pedido #'.$this->order->id)->from($this->order->shop->email, $this->order->shop->name)->view('emails.receipt');
    }
}
