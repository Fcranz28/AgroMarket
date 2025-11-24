<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;

class ProductController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        return view('products.index', compact('categories'));
    }

    public function show(string $slug)
    {
        $product = Product::with('units')->where('slug', $slug)->firstOrFail();
        return view('products.show', compact('product'));
    }
}
