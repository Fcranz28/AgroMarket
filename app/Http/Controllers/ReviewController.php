<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ReviewController extends Controller
{
    public function store(Request $request, Product $product)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
            'image' => 'nullable|image|max:2048', // 2MB Max
        ]);

        $data = [
            'user_id' => Auth::id(),
            'product_id' => $product->id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ];

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('reviews', 'public');
            $data['image_path'] = $path;
        }

        Review::create($data);

        return back()->with('success', 'Reseña enviada correctamente.');
    }
}
