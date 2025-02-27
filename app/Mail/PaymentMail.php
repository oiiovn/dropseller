<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PaymentMail extends Mailable
{
    use Queueable, SerializesModels;

    public $order; 
    public function __construct($order)
    {
        $this->order = $order;
    }
    public function build()
    {
        return $this->from(env('MAIL_FROM_ADDRESS'))
                    ->subject('Đơn hàng của bạn đã được thanh toán')
                    ->view('emails.payment_mail')
                    ->with([
                        'order' => $this->order,
                    ]);
    }
}
