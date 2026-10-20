<?php

namespace App\Modules\Catalog\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Catalog\Models\Category;
use App\Modules\Catalog\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use OpenApi\Attributes as OA;

class SearchController extends Controller
{
    /**
     * Maximum number of products returned by a search.
     */
    private const RESULT_LIMIT = 60;

    public function index(Request $request): View
    {
        $query = (string) $request->query('q', '');
        $categorySlug = (string) $request->query('category', 'all');

        $categories = Category::all();

        return view('search.index', compact('categories', 'query', 'categorySlug'));
    }

    #[OA\Get(
        path: '/api/search',
        summary: 'Buscar productos',
        description: 'Busca en nombre y descripción (los comodines % y _ se toman literalmente). Devuelve como máximo 60 resultados.',
        tags: ['Catálogo'],
        parameters: [
            new OA\Parameter(name: 'q', in: 'query', required: false, schema: new OA\Schema(type: 'string', example: 'papa')),
            new OA\Parameter(name: 'category', in: 'query', required: false, description: 'Slug de la categoría (all/todos = todas)', schema: new OA\Schema(type: 'string')),
            new OA\Parameter(ref: '#/components/parameters/sort'),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Resultados', content: new OA\JsonContent(properties: [
                new OA\Property(property: 'products', type: 'array', items: new OA\Items(ref: '#/components/schemas/SearchProduct')),
                new OA\Property(property: 'total', type: 'integer', example: 3),
            ])),
            new OA\Response(ref: '#/components/responses/TooManyRequests', response: 429),
        ],
    )]
    public function search(Request $request): JsonResponse
    {
        $term = trim((string) $request->query('q', ''));
        $categorySlug = (string) $request->query('category', '');

        $products = Product::query()
            ->with('category')
            ->when($term !== '', fn ($query) => $query->search($term))
            ->when(! in_array($categorySlug, ['', 'all', 'todos'], true), fn ($query) => $query->whereHas(
                'category',
                fn ($query) => $query->where('slug', $categorySlug),
            ))
            ->tap(fn ($query) => match ($request->query('sort')) {
                'price-asc' => $query->orderBy('price'),
                'price-desc' => $query->orderByDesc('price'),
                'name-asc' => $query->orderBy('name'),
                'name-desc' => $query->orderByDesc('name'),
                default => $query->latest(),
            })
            ->limit(self::RESULT_LIMIT)
            ->get()
            ->map(fn (Product $product) => [
                'id' => $product->id,
                'slug' => $product->slug,
                'name' => $product->name,
                'price' => $product->price,
                'unit' => $product->unit ?? 'kg',
                'image_path' => $product->image_path ? asset('storage/'.$product->image_path) : null,
                'image_url' => $product->image_url,
                'category_name' => $product->category?->name,
            ]);

        return response()->json(['products' => $products, 'total' => $products->count()]);
    }

    #[OA\Get(
        path: '/api/suggestions',
        summary: 'Sugerencias del buscador',
        description: 'Hasta 5 productos cuyo nombre contiene el término. Con menos de 2 caracteres devuelve una lista vacía.',
        tags: ['Catálogo'],
        parameters: [
            new OA\Parameter(name: 'q', in: 'query', required: true, schema: new OA\Schema(type: 'string', minLength: 2, example: 'pa')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Sugerencias', content: new OA\JsonContent(properties: [
                new OA\Property(property: 'suggestions', type: 'array', items: new OA\Items(properties: [
                    new OA\Property(property: 'id', type: 'integer'),
                    new OA\Property(property: 'name', type: 'string'),
                    new OA\Property(property: 'slug', type: 'string'),
                    new OA\Property(property: 'image_path', type: 'string', nullable: true),
                    new OA\Property(property: 'price', type: 'string'),
                ])),
            ])),
            new OA\Response(ref: '#/components/responses/TooManyRequests', response: 429),
        ],
    )]
    public function suggestions(Request $request): JsonResponse
    {
        $term = trim((string) $request->query('q', ''));

        if (mb_strlen($term) < 2) {
            return response()->json(['suggestions' => []]);
        }

        $products = Product::search($term, includeDescription: false)
            ->select(['id', 'name', 'slug', 'image_path', 'price'])
            ->limit(5)
            ->get();

        return response()->json(['suggestions' => $products]);
    }
}
