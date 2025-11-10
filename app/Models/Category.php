<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany; // <-- Asegúrate de importar esto

class Category extends Model
{
    use HasFactory;

    // ... (El resto de tu modelo aquí) ...

    /**
     * Define la relación: Una Categoría tiene muchos Productos.
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}