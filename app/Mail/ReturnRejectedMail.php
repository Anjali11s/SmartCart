<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReturnRejectedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $reason;

    public function __construct(Order $order, $reason)
    {
        $this->order = $order;
        $this->reason = $reason;
    }

    public function build()
    {
        return $this->subject('Return Request Update - SmartCart #' . $this->order->id)
                    ->markdown('emails.return-rejected');
    }
}