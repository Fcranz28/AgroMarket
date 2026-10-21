<?php

namespace App\Modules\Orders\Notifications;

use App\Modules\Orders\Models\Order;
use App\Modules\Orders\Models\OrderItem;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;

/**
 * Tells a farmer which of their products were sold in a new order.
 */
class NewOrderForFarmerNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @param  Collection<int, OrderItem>  $items  the farmer's items of the order
     */
    public function __construct(public Order $order, public Collection $items) {}

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
            ->subject("Nuevo pedido #{$this->order->id}")
            ->greeting('¡Tienes un nuevo pedido!')
            ->line('Un cliente compró estos productos tuyos:');

        foreach ($this->items as $item) {
            $message->line("• {$item->quantity} × {$item->product?->name} ({$item->unit})");
        }

        return $message
            ->line("Envío a: {$this->order->shipping_address}")
            ->action('Ver el pedido', route('farmer.orders.show', $this->order));
    }
}
