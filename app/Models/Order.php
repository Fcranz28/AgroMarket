<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
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
        'document_number'
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
    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }
}
