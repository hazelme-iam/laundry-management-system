<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class OrderUpdateNotification extends Notification
{
    use Queueable;

    protected $order;
    protected $message;

    public function __construct(Order $order, $message)
    {
        $this->order = $order;
        $this->message = $message;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'order_id' => $this->order->id,
            'title' => 'Order Update',
            'message' => $this->message,
            'type' => 'update',
            'data' => [
                'order_id' => $this->order->id,
                'status' => $this->order->status,
                'custom_message' => $this->message,
            ]
        ];
    }
}
