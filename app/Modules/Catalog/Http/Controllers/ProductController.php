<?php

namespace App\Modules\Catalog\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Catalog\Models\Category;
use App\Modules\Catalog\Models\Product;

class ProductController extends Controller
{
    public function index()
    {
        $categories = Category::all();

        return view('products.index', compact('categories'));
    }

    public function show(string $slug)
    {
        $product = Product::with(['units', 'reviews.user'])->where('slug', $slug)->firstOrFail();

        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->take(4)
            ->get();

        return view('products.show', compact('product', 'relatedProducts'));
    }
}
