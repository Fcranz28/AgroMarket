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

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'unit',
        'stock',
        'image_path',
        'category_id',
        'user_id'
    ];
    public function user(){
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
     * Obtiene la URL completa de la imagen
     */
    public function getImageUrlAttribute(): string
    {
        if ($this->image_path) {
            return asset('storage/' . $this->image_path);
        }
        return asset('img/placeholder.png');
    }
        
}