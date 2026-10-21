<?php

namespace App\Modules\Catalog\Models;

use App\Modules\Catalog\Database\Factories\CategoryFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany; // <-- Asegúrate de importar esto

#[UseFactory(CategoryFactory::class)]
class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug'];

    // ... (El resto de tu modelo aquí) ...

    /**
     * Define la relación: Una Categoría tiene muchos Productos.
     *
     * @return HasMany<Product, $this>
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
