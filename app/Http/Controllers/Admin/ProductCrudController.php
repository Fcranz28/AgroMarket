<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminProductRequest;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductCrudController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $products = Product::with('category')->get();

        return view('admin.products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $categories = Category::all();

        return view('admin.products.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AdminProductRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request) {
            $product = Product::create([
                ...$request->safe()->only(['category_id', 'name', 'description']),
                'user_id' => $request->user()->id,
                'slug' => Str::slug($request->validated('name')).'-'.Str::random(8),
                'unit' => $request->units()[0]['unit'],
                'price' => 0,
                'stock' => 0,
                'image_path' => $request->file('image')?->store('products', 'public'),
            ]);

            $product->syncUnits($request->units());
        });

        return redirect()->route('admin.productos.index')
            ->with('success', 'Producto creado correctamente');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $producto): View
    {
        return view('admin.products.show', ['product' => $producto]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $producto): View
    {
        $categories = Category::all();

        return view('admin.products.edit', ['product' => $producto, 'categories' => $categories]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AdminProductRequest $request, Product $producto): RedirectResponse
    {
        $oldImagePath = $producto->image_path;
        $newImagePath = $request->file('image')?->store('products', 'public');

        DB::transaction(function () use ($request, $producto, $newImagePath) {
            $producto->fill($request->safe()->only(['category_id', 'name', 'description']));

            if ($newImagePath) {
                $producto->image_path = $newImagePath;
            }

            $producto->save();
            $producto->syncUnits($request->units());
        });

        if ($newImagePath && $oldImagePath) {
            Storage::disk('public')->delete($oldImagePath);
        }

        return redirect()->route('admin.productos.index')
            ->with('success', 'Producto actualizado correctamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $producto): RedirectResponse
    {
        Storage::disk('public')->delete(array_filter([
            $producto->image_path,
            ...$producto->images()->pluck('image_path'),
        ]));

        $producto->delete();

        return redirect()->route('admin.productos.index')
            ->with('success', 'Producto eliminado correctamente');
    }
}
