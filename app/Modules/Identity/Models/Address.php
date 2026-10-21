<?php

namespace App\Modules\Identity\Models;

use App\Modules\Identity\Policies\AddressPolicy;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[UsePolicy(AddressPolicy::class)]
class Address extends Model
{
    /**
     * Saved shipping addresses allowed per user.
     */
    public const MAX_PER_USER = 3;

    protected $fillable = [
        'user_id',
        'address',
        'latitude',
        'longitude',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
