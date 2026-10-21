<?php

namespace App\Modules\Catalog\Models;

use Illuminate\Database\Eloquent\Attributes\UseFactory;
use App\Modules\Catalog\Database\Factories\ProductUnitFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[UseFactory(ProductUnitFactory::class)]
class ProductUnit extends Model
{
    /** @use HasFactory<\App\Modules\Catalog\Database\Factories\ProductUnitFactory> */
    use HasFactory;

    protected $fillable = [
        'product_id',
        'unit',
        'price',
        'stock',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
