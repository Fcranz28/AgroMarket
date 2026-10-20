<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    /** @use HasFactory<\Database\Factories\OrderFactory> */
    use HasFactory;

    public const STATUS_CANCELLED = 'cancelled';

    public const STATUS_DELIVERED = 'delivered';

    /**
     * @var list<string>
     */
    public const STATUSES = ['pending', 'processing', 'shipped', self::STATUS_DELIVERED, self::STATUS_CANCELLED];

    /**
     * Orders in these statuses can no longer change.
     *
     * @var list<string>
     */
    public const FINAL_STATUSES = [self::STATUS_DELIVERED, self::STATUS_CANCELLED];

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
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the items for the order
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the invoice for the order
     */
    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class);
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
        return in_array($this->status, self::FINAL_STATUSES, true);
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
