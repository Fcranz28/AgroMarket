<?php

namespace App\Modules\Catalog\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\Catalog\Http\Resources\CatalogProductResource;
use App\Modules\Catalog\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request; // <-- 1. Importa Request
use OpenApi\Attributes as OA;

class ProductApiController extends Controller
{
    /**
     * Maneja la solicitud de productos con filtros y ordenamiento.
     */
    #[OA\Get(
        path: '/api/productos',
        summary: 'Catálogo de productos',
        tags: ['Catálogo'],
        parameters: [
            new OA\Parameter(name: 'category', in: 'query', required: false, description: 'Slug de la categoría (all/todos = todas)', schema: new OA\Schema(type: 'string', example: 'tuberculos')),
            new OA\Parameter(ref: '#/components/parameters/sort'),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Productos publicados', content: new OA\JsonContent(properties: [
                new OA\Property(property: 'products', type: 'array', items: new OA\Items(ref: '#/components/schemas/CatalogProduct')),
            ])),
            new OA\Response(ref: '#/components/responses/TooManyRequests', response: 429),
        ],
    )]
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
            'products' => CatalogProductResource::collection($products),
        ]);
    }
}
