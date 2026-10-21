<?php

namespace App\Modules\Catalog\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Catalog\Http\Requests\StoreReviewRequest;
use App\Modules\Catalog\Models\Product;
use App\Modules\Catalog\Models\Review;
use Illuminate\Http\RedirectResponse;

class ReviewController extends Controller
{
    public function store(StoreReviewRequest $request, Product $product): RedirectResponse
    {
        Review::create([
            'user_id' => $request->user()->id,
            'product_id' => $product->id,
            'rating' => $request->validated('rating'),
            'comment' => $request->validated('comment'),
            'image_path' => $request->file('image')?->store('reviews', 'public'),
        ]);

        return back()->with('success', 'Reseña enviada correctamente.');
    }
}
