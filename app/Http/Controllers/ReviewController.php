<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReviewRequest;
use App\Models\Product;
use App\Models\Review;
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
