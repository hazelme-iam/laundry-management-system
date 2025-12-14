<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class OrderReceiptNotification extends Notification
{
    use Queueable;

    protected $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'order_id' => $this->order->id,
            'title' => 'Order Receipt',
            'message' => 'Your receipt for Order #' . str_pad($this->order->id, 5, '0', STR_PAD_LEFT) . ' is ready',
            'type' => 'receipt',
            'data' => [
                'order_id' => $this->order->id,
                'total_amount' => $this->order->total_amount,
                'amount_paid' => $this->order->amount_paid,
                'status' => $this->order->status,
            ]
        ];
    }
}
