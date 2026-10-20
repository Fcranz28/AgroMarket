<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

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
