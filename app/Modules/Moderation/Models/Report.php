<?php

namespace App\Modules\Moderation\Models;

use App\Modules\Catalog\Models\Product;
use App\Modules\Identity\Models\User;
use App\Modules\Moderation\Enums\ReportStatus;
use App\Modules\Orders\Models\Order;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property ReportStatus $status
 */
class Report extends Model
{
    /**
     * Reasons a buyer can pick, with their label.
     */
    public const REASONS = [
        'damaged' => 'Producto dañado',
        'wrong_item' => 'Producto incorrecto',
        'quality' => 'Mala calidad',
        'expired' => 'Producto vencido',
        'other' => 'Otro',
    ];

    protected $fillable = [
        'user_id',
        'product_id',
        'order_id',
        'reason',
        'description',
        'evidence',
        'status',
        'admin_notes',
    ];

    protected $casts = [
        'evidence' => 'array',
        'status' => ReportStatus::class,
    ];

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<Product, $this>
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    /**
     * @return BelongsTo<Order, $this>
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
