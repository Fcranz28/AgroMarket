<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; 
use Illuminate\Support\Str;

class UserProductController extends Controller
{
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
    public function create()
    {
        $categories = \App\Models\Category::all();
        return view('dashboard.products.create');
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
            'unit' => 'required|string|max:50',
            'stock' => 'required|integer|min:0',
            'image_path' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $path = $request->file('image_path')->store('products', 'public');
        $validatedData['image_path'] = $path;
        $validatedData['slug'] = Str::slug($request->name) . '-' . uniqid();

        auth()->user()->products()->create($validatedData);
        return redirect()->route('dashboard.productos.index')->with('success', '¡Producto creado!');
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
            'unit' => 'required|string|max:50',
            'stock' => 'required|integer|min:0',
            'image_path' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if ($request->hasFile('image_path')) {
            // A. Borrar la imagen antigua (si existe)
            if ($product->image_path) {
                Storage::disk('public')->delete($product->image_path);
            }
            // B. Guardar la nueva imagen
            $path = $request->file('image_path')->store('products', 'public');
            $validatedData['image_path'] = $path;
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
        if ($product->image_path) {
            Storage::disk('public')->delete($product->image_path);
        }
        $product->delete();
        return redirect()->route('dashboard.productos.index')->with('success', '¡Producto eliminado!');
    }
}
