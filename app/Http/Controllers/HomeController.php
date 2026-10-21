<?php

namespace App\Http\Controllers;

use App\Modules\Catalog\Models\Category;
use App\Modules\Catalog\Models\Product;
use Illuminate\View\View;

class HomeController extends Controller
{
    private const FEATURED_COUNT = 8;

    private const SUGGESTED_COUNT = 8;

    private const CATEGORY_COUNT = 6;

    public function index(): View
    {
        $featuredProducts = Product::query()
            ->with(['category', 'units'])
            ->where('stock', '>', 0)
            ->latest()
            ->take(self::FEATURED_COUNT)
            ->get();

        $suggestedProducts = Product::query()
            ->with(['category', 'units'])
            ->whereNotIn('id', $featuredProducts->modelKeys())
            ->inRandomOrder()
            ->take(self::SUGGESTED_COUNT)
            ->get();

        $categories = Category::query()->whereHas('products')->orderBy('name')->take(self::CATEGORY_COUNT)->get();

        return view('home', compact('featuredProducts', 'suggestedProducts', 'categories'));
    }
}
