<?php

namespace App\Modules\Orders\Listeners;

use App\Modules\Orders\Events\OrderPlaced;
use App\Modules\Orders\Models\OrderItem;
use App\Modules\Orders\Notifications\NewOrderForFarmerNotification;
use App\Modules\Orders\Notifications\OrderPlacedNotification;

/**
 * Confirms the purchase to the buyer and tells each farmer what they sold. The mails
 * themselves are queued (the notifications implement ShouldQueue).
 */
class SendOrderPlacedNotifications
{
    public function handle(OrderPlaced $event): void
    {
        $order = $event->order->loadMissing('items.product.user');

        $order->notifyBuyer(new OrderPlacedNotification($order));

        $order->items
            ->groupBy(fn (OrderItem $item) => $item->product?->user_id)
            ->each(function ($items) use ($order) {
                $farmer = $items->first()?->product?->user;

                $farmer?->notify(new NewOrderForFarmerNotification($order, $items->values()));
            });
    }
}
