<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderMail extends Mailable
{
    use Queueable, SerializesModels;

    public $order; // Biến lưu đơn hàng
    public $orderDetail; // Biến lưu đơn hàng

    public function __construct($order, $orderDetail)
    {
        $this->order = $order;
        $this->orderDetail = $orderDetail;
    }

    public function build()
    {
        return $this->from(env('MAIL_FROM_ADDRESS'))
                    ->subject('Bạn có đơn hàng mới')
                    ->view('emails.order_mail')
                    ->with([
                        'order' => $this->order,
                        'orderDetail' => $this->orderDetail,
                    ]);
    }
}
