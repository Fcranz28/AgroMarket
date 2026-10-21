<?php

namespace App\Modules\Orders\Models;

use App\Modules\Identity\Models\User;
use App\Modules\Orders\Database\Factories\OrderFactory;
use App\Modules\Orders\Enums\OrderStatus;
use App\Modules\Orders\Enums\PaymentStatus;
use App\Modules\Orders\Policies\OrderPolicy;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Notification as NotificationFacade;

/**
 * The invoice relation is registered by the Invoicing module with
 * Order::resolveRelationUsing(), so Orders does not depend on it.
 *
 * @property OrderStatus $status
 * @property PaymentStatus $payment_status
 */
#[UseFactory(OrderFactory::class)]
#[UsePolicy(OrderPolicy::class)]
class Order extends Model
{
    /** @use HasFactory<\App\Modules\Orders\Database\Factories\OrderFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => OrderStatus::class,
            'payment_status' => PaymentStatus::class,
        ];
    }

    protected $fillable = [
        'user_id',
        'total',
        'status',
        'shipping_address',
        'phone',
        'stripe_payment_intent_id',
        'payment_status',
        'guest_name',
        'guest_lastname',
        'guest_email',
        'document_type',
        'document_number',
        'uuid',
    ];

    /**
     * Get the user that owns the order
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the items for the order
     *
     * @return HasMany<OrderItem, $this>
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Whether every item of the order belongs to the given farmer.
     */
    public function isFullySoldBy(User $farmer): bool
    {
        return $this->items()->exists()
            && ! $this->items()->whereDoesntHave('product', fn ($query) => $query->where('user_id', $farmer->id))->exists();
    }

    /**
     * Whether the given farmer sells at least one item of the order.
     */
    public function hasItemsSoldBy(User $farmer): bool
    {
        return $this->items()->whereHas('product', fn ($query) => $query->where('user_id', $farmer->id))->exists();
    }

    public function isFinal(): bool
    {
        return $this->status->isFinal();
    }

    /**
     * Notify the buyer: the account owner, or the e-mail given at checkout for guests.
     */
    public function notifyBuyer(Notification $notification): void
    {
        if ($this->user) {
            $this->user->notify($notification);

            return;
        }

        NotificationFacade::route('mail', $this->guest_email)->notify($notification);
    }

    /**
     * The "booted" method of the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->uuid)) {
                $order->uuid = (string) \Illuminate\Support\Str::uuid();
            }
        });
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'uuid';
    }
}
