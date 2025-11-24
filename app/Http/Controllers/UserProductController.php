<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; 
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class UserProductController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = auth()->user()->products()->latest()->get();
        return view('dashboard.products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (!auth()->user()->isVerified() && !auth()->user()->isAdmin()) {
            return redirect()->route('dashboard')->with('error', 'Debes ser un agricultor verificado para publicar productos.');
        }
        $categories = \App\Models\Category::all();
        return view('dashboard.products.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'unit' => 'required|array|min:1',
            'stock' => 'required|array|min:1',
            'image' => 'required',
            'image.*' => 'image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        // Convert unit and stock arrays to comma-separated strings
        $validatedData['unit'] = implode(', ', $request->unit);
        $validatedData['stock'] = implode(', ', $request->stock);

        // Handle Image Upload
        if ($request->hasFile('image')) {
            $images = $request->file('image');
            
            // If it's an array (multiple files), take the first one as main image
            if (is_array($images)) {
                $mainImage = $images[0];
                $imagePath = $mainImage->store('products', 'public');
                $validatedData['image_path'] = $imagePath;
            } else {
                // Single file fallback
                $imagePath = $images->store('products', 'public');
                $validatedData['image_path'] = $imagePath;
            }
        }

        $validatedData['slug'] = Str::slug($request->name) . '-' . uniqid();

        $product = Auth::user()->products()->create($validatedData);

        // Save all images to product_images table
        if ($request->hasFile('image')) {
            $images = $request->file('image');
            if (!is_array($images)) {
                $images = [$images];
            }

            foreach ($images as $image) {
                $path = $image->store('products', 'public');
                $product->images()->create(['image_path' => $path]);
            }
        }
        return redirect()->route('dashboard.productos.index')->with('success', 'Producto creado con éxito.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        if ($product->user_id !== auth()->id()) {
            abort(403, 'No estás autorizado para ver este producto.');
        }
        return view('dashboard.products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $this->authorize('update', $product);
        $categories = \App\Models\Category::all();
        return view('dashboard.products.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $this->authorize('update', $product);
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'unit' => 'required|array|min:1',
            'stock' => 'required|array|min:1',
            'image' => 'nullable',
            'image.*' => 'image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        // Convert unit and stock arrays to comma-separated strings
        $validatedData['unit'] = implode(', ', $request->unit);
        $validatedData['stock'] = implode(', ', $request->stock);

        if ($request->hasFile('image')) {
            $images = $request->file('image');
            if (!is_array($images)) {
                $images = [$images];
            }

            // If product has no main image, use the first new one
            if (!$product->image_path && count($images) > 0) {
                $validatedData['image_path'] = $images[0]->store('products', 'public');
            }

            // Add all new images to gallery
            foreach ($images as $image) {
                $path = $image->store('products', 'public');
                $product->images()->create(['image_path' => $path]);
            }
        }
        
        // Handle deletion of specific images (if implemented in view)
        if ($request->has('delete_images')) {
            foreach ($request->delete_images as $imageId) {
                $img = \App\Models\ProductImage::find($imageId);
                if ($img && $img->product_id == $product->id) {
                    Storage::disk('public')->delete($img->image_path);
                    $img->delete();
                }
            }
        }

        if ($request->name !== $product->name) {
            $validatedData['slug'] = Str::slug($request->name) . '-' . uniqid();
        }

        $product->update($validatedData);

        return redirect()->route('dashboard.productos.index')->with('success', '¡Producto actualizado!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $this->authorize('delete', $product);
        
        // Delete main image
        if ($product->image_path) {
            Storage::disk('public')->delete($product->image_path);
        }

        // Delete gallery images
        foreach ($product->images as $image) {
            Storage::disk('public')->delete($image->image_path);
        }

        $product->delete();
        return redirect()->route('dashboard.productos.index')->with('success', '¡Producto eliminado!');
    }
}
