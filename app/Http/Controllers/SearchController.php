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
