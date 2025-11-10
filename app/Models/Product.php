<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // <-- Asegúrate de importar esto

class Product extends Model
{
    use HasFactory;

    // ... (El resto de tu modelo aquí) ...

    /**
     * Define la relación: Un Producto pertenece a una Categoría.
     * Esto le permite a Eloquent usar la 'category_id' que definiste.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}