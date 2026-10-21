<?php

namespace App\Modules\Orders\Notifications;

use App\Modules\Orders\Enums\OrderStatus;
use App\Modules\Orders\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Tells the buyer that the farmer moved their order to a new status.
 */
class OrderStatusChangedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Order $order, public OrderStatus $status) {}

    /**
     * @return list<string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject("Tu pedido #{$this->order->id}: {$this->status->label()}")
            ->line("Tu pedido ahora está: **{$this->status->label()}**.");

        if ($this->status === OrderStatus::Cancelled) {
            $message->line('Si ya pagaste, el reembolso se procesa automáticamente en tu tarjeta.');
        }

        if ($this->order->user_id) {
            $message->action('Ver mi pedido', route('orders.show', $this->order));
        }

        return $message;
    }
}
