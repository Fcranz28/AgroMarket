<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;
    /**
     * Los atributos que se pueden asignar masivamente.
     *
     * @var array
     */

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'stock' => 'integer',
        ];
    }

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'unit',
        'stock',
        'image_path',
        'category_id',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }

    public function units(): HasMany
    {
        return $this->hasMany(ProductUnit::class);
    }

    /**
     * Define la relación: Un Producto pertenece a una Categoría.
     * Esto le permite a Eloquent usar la 'category_id' que definiste.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Replace the product units, keeping the ids of units that still exist so carts and
     * order history that reference them stay valid. The base price, unit and stock shown
     * in the catalog are derived from the units.
     *
     * @param  list<array{unit: string, stock: int, price: string}>  $units
     */
    public function syncUnits(array $units): void
    {
        $this->units()->whereNotIn('unit', array_column($units, 'unit'))->delete();

        foreach ($units as $unit) {
            $this->units()->updateOrCreate(['unit' => $unit['unit']], $unit);
        }

        $this->update([
            'unit' => $units[0]['unit'],
            'price' => min(array_column($units, 'price')),
            'stock' => array_sum(array_column($units, 'stock')),
        ]);
    }

    /**
     * Obtiene la URL completa de la imagen
     */
    public function getImageUrlAttribute(): string
    {
        if ($this->image_path) {
            return asset('storage/'.$this->image_path);
        }

        return asset('img/placeholder.png');
    }
}
