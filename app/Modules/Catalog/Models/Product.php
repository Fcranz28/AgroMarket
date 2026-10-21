<?php

namespace App\Modules\Catalog\Models;

use App\Modules\Catalog\Database\Factories\ProductFactory;
use App\Modules\Catalog\Policies\ProductPolicy;
use App\Modules\Identity\Models\User;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[UseFactory(ProductFactory::class)]
#[UsePolicy(ProductPolicy::class)]
class Product extends Model
{
    use HasFactory, SoftDeletes;

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

    /**
     * Los atributos que se pueden asignar masivamente.
     *
     * @var list<string>
     */
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

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany<ProductImage, $this>
     */
    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }

    /**
     * @return HasMany<ProductUnit, $this>
     */
    public function units(): HasMany
    {
        return $this->hasMany(ProductUnit::class);
    }

    /**
     * Define la relación: Un Producto pertenece a una Categoría.
     * Esto le permite a Eloquent usar la 'category_id' que definiste.
     *
     * @return BelongsTo<Category, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * @return HasMany<Review, $this>
     */
    public function reviews(): HasMany
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
     * Products published by the given farmer.
     */
    public function scopeOwnedBy(Builder $query, User $farmer): void
    {
        $query->where('user_id', $farmer->id);
    }

    /**
     * Match the term against the name or description, treating LIKE wildcards literally.
     */
    public function scopeSearch(Builder $query, string $term, bool $includeDescription = true): void
    {
        $like = '%'.addcslashes($term, '\\%_').'%';

        $query->where(function (Builder $query) use ($like, $includeDescription) {
            $query->where('name', 'like', $like);

            if ($includeDescription) {
                $query->orWhere('description', 'like', $like);
            }
        });
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
