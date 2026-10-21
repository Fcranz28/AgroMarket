<?php

namespace App\Http\Controllers;

use App\Modules\Catalog\Models\Product;
use Illuminate\View\View;

class HomeController extends Controller
{
    private const FEATURED_COUNT = 8;

    private const SUGGESTED_COUNT = 8;

    public function index(): View
    {
        $featuredProducts = Product::query()
            ->with('category')
            ->where('stock', '>', 0)
            ->latest()
            ->take(self::FEATURED_COUNT)
            ->get();

        $suggestedProducts = Product::query()
            ->with('category')
            ->whereNotIn('id', $featuredProducts->modelKeys())
            ->inRandomOrder()
            ->take(self::SUGGESTED_COUNT)
            ->get();

        return view('home', compact('featuredProducts', 'suggestedProducts'));
    }
}
