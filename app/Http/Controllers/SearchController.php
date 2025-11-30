<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->query('q', '');
        $categorySlug = $request->query('category', 'all');
        
        $categories = Category::all();
        
        return view('search.index', compact('categories', 'query', 'categorySlug'));
    }
    
    public function search(Request $request)
    {
        $query = $request->query('q', '');
        $categorySlug = $request->query('category', '');
        $sort = $request->query('sort', 'featured');
        
        $productsQuery = Product::query();
        
        // Filter by search query
        if (!empty($query)) {
            $productsQuery->where('name', 'LIKE', "%{$query}%")
                ->orWhere('description', 'LIKE', "%{$query}%");
        }
        
        // Filter by category
        if (!empty($categorySlug) && $categorySlug !== 'all') {
            $productsQuery->whereHas('category', function($q) use ($categorySlug) {
                $q->where('slug', $categorySlug);
            });
        }
        
        // Sorting
        switch ($sort) {
            case 'price-asc':
                $productsQuery->orderBy('price', 'asc');
                break;
            case 'price-desc':
                $productsQuery->orderBy('price', 'desc');
                break;
            case 'name-asc':
                $productsQuery->orderBy('name', 'asc');
                break;
            case 'name-desc':
                $productsQuery->orderBy('name', 'desc');
                break;
            default:
                $productsQuery->orderBy('created_at', 'desc');
        }
        
        $products = $productsQuery->with('category')->get()->map(function($product) {
            return [
                'id' => $product->id,
                'slug' => $product->slug,
                'name' => $product->name,
                'price' => $product->price,
                'unit' => $product->unit ?? 'kg',
                'image_path' => $product->image_path ? asset('storage/' . $product->image_path) : null,
                'image_url' => $product->image_url,
                'category_name' => $product->category->name ?? null,
            ];
        });
        
        return response()->json(['products' => $products]);
    }
    
    public function suggestions(Request $request)
    {
        $query = $request->query('q', '');
        
        if (strlen($query) < 2) {
            return response()->json(['suggestions' => []]);
        }
        
        $products = Product::where('name', 'LIKE', "%{$query}%")
            ->select(['id', 'name', 'slug', 'image_path', 'price'])
            ->limit(5)
            ->get();
        
        return response()->json(['suggestions' => $products]);
    }
}
