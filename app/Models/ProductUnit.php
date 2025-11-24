<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductUnit extends Model
{
    protected $fillable = [
        'product_id',
        'unit',
        'price',
        'stock'
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
