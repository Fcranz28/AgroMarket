<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request; // <-- 1. Importa Request

class ProductApiController extends Controller
{
    /**
     * Maneja la solicitud de productos con filtros y ordenamiento.
     */
    public function index(Request $request): JsonResponse
    {
        // 2. Lee los parámetros de la URL (?category=...&sort=...)
        $categorySlug = $request->query('category');
        $sort = $request->query('sort');

        // 3. Inicia la consulta del producto
        $query = Product::query();

        // 4. Filtra por categoría (si no es "all" o "todos")
        // Esto funciona gracias a la columna 'slug' que definiste
        if ($categorySlug && $categorySlug !== 'all' && $categorySlug !== 'todos') {
            $query->whereHas('category', function ($q) use ($categorySlug) {
                $q->where('slug', $categorySlug);
            });
        }

        // 5. Aplica el ordenamiento
        switch ($sort) {
            case 'price-asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price-desc':
                $query->orderBy('price', 'desc');
                break;
            case 'name-asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name-desc':
                $query->orderBy('name', 'desc');
                break;
            default: // 'featured' (destacados) o por defecto
                $query->latest('id'); // Muestra los más nuevos primero
        }

        // 6. Ejecuta la consulta
        $products = $query->select(['id', 'category_id', 'name', 'slug', 'price', 'unit', 'stock', 'image_path'])
                          ->get();

        return response()->json([
            'products' => $products,
        ]);
    }
    
    /**
     * Search products with filters
     */
    public function search(Request $request): JsonResponse
    {
        $searchQuery = $request->query('q', '');
        $categorySlug = $request->query('category', 'all');
        $sort = $request->query('sort', 'featured');
        
        // Start query
        $query = Product::query();
        
        // Category filter (apply first)
        if ($categorySlug && $categorySlug !== 'all' && $categorySlug !== 'todos') {
            $query->whereHas('category', function ($q) use ($categorySlug) {
                $q->where('slug', $categorySlug);
            });
        }
        
        // Search filter (group conditions properly)
        if (!empty($searchQuery)) {
            $query->where(function($q) use ($searchQuery) {
                $q->where('name', 'LIKE', "%{$searchQuery}%")
                  ->orWhere('description', 'LIKE', "%{$searchQuery}%");
            });
        }
        
        // Apply sorting
        switch ($sort) {
            case 'price-asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price-desc':
                $query->orderBy('price', 'desc');
                break;
            case 'name-asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name-desc':
                $query->orderBy('name', 'desc');
                break;
            default:
                $query->latest('id');
        }
        
        // Execute query
        $products = $query->select(['id', 'category_id', 'name', 'slug', 'price', 'unit', 'stock', 'image_path'])
                          ->get();
        
        return response()->json([
            'products' => $products,
            'total' => $products->count(),
        ]);
    }
}