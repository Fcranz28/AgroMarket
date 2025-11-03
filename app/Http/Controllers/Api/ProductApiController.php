<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;

class ProductApiController extends Controller
{
    public function index(): JsonResponse
    {
        $products = Product::query()
            ->select(['id', 'category_id', 'name', 'slug', 'price', 'unit', 'stock', 'image_path'])
            ->latest('id')
            ->get();

        return response()->json([
            'products' => $products,
        ]);
    }
}
