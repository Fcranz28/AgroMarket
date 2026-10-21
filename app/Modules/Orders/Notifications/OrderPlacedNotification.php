<?php

namespace App\Modules\Orders\Notifications;

use App\Modules\Orders\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Purchase confirmation for the buyer (registered user or guest).
 */
class OrderPlacedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Order $order) {}

    /**
     * @return list<string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $this->order->loadMissing('items.product');

        $message = (new MailMessage)
            ->subject("Recibimos tu pedido #{$this->order->id}")
            ->greeting('¡Gracias por tu compra!')
            ->line('Tu pago fue confirmado y ya avisamos a los agricultores.');

        foreach ($this->order->items as $item) {
            $message->line("• {$item->quantity} × {$item->product?->name} ({$item->unit}) — S/. ".number_format((float) $item->price * $item->quantity, 2));
        }

        $message->line('**Total: S/. '.number_format((float) $this->order->total, 2).'** (IGV incluido)')
            ->line("Envío a: {$this->order->shipping_address}");

        if ($this->order->user_id) {
            $message->action('Ver mi pedido', route('orders.show', $this->order));
        }

        return $message->line('Te enviaremos tu comprobante electrónico en cuanto esté listo.');
    }
}
