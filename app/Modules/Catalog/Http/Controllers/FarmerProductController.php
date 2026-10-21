<?php

namespace App\Modules\Catalog\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Catalog\Http\Requests\FarmerProductRequest;
use App\Modules\Catalog\Models\Category;
use App\Modules\Catalog\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class FarmerProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $products = Product::ownedBy(auth()->user())->with('category')->latest()->paginate(15);

        return view('dashboard.products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View|RedirectResponse
    {
        if (auth()->user()->cannot('create', Product::class)) {
            return redirect()->route('farmer.dashboard')->with('error', 'Debes ser un agricultor verificado para publicar productos.');
        }

        $categories = Category::all();

        return view('dashboard.products.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(FarmerProductRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request) {
            $product = Product::create([
                ...$request->safe()->only(['name', 'description', 'category_id']),
                'user_id' => $request->user()->id,
                'slug' => Str::slug($request->validated('name')).'-'.Str::random(8),
                'unit' => $request->units()[0]['unit'],
                'price' => 0,
                'stock' => 0,
            ]);

            $product->syncUnits($request->units());
            $this->storeImages($product, $request->file('image', []));
        });

        return redirect()->route('dashboard.productos.index')->with('success', 'Producto creado con éxito.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $producto): View
    {
        $this->authorize('view', $producto);

        return view('dashboard.products.show', ['product' => $producto->load(['units', 'images', 'category'])]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $producto): View
    {
        $this->authorize('update', $producto);

        $categories = Category::all();

        return view('dashboard.products.edit', ['producto' => $producto->load(['units', 'images']), 'categories' => $categories]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(FarmerProductRequest $request, Product $producto): RedirectResponse
    {
        $this->authorize('update', $producto);

        DB::transaction(function () use ($request, $producto) {
            $imagesToDelete = $producto->images()->whereIn('id', $request->validated('delete_images', []))->get();

            foreach ($imagesToDelete as $image) {
                Storage::disk('public')->delete($image->image_path);

                if ($producto->image_path === $image->image_path) {
                    $producto->image_path = null;
                }

                $image->delete();
            }

            $producto->fill($request->safe()->only(['name', 'description', 'category_id']))->save();
            $producto->syncUnits($request->units());
            $this->storeImages($producto, $request->file('image', []));
        });

        return redirect()->route('dashboard.productos.index')->with('success', '¡Producto actualizado con éxito!');
    }

    /**
     * Remove the specified resource from the catalog.
     *
     * Products are soft deleted and keep their images because past orders, invoices and
     * reports still show them.
     */
    public function destroy(Product $producto): RedirectResponse
    {
        $this->authorize('delete', $producto);

        $producto->delete();

        return redirect()->route('dashboard.productos.index')->with('success', '¡Producto eliminado!');
    }

    /**
     * Store gallery images; the first one becomes the main image when the product has none.
     *
     * @param  array<int, UploadedFile>  $images
     */
    private function storeImages(Product $product, array $images): void
    {
        foreach ($images as $image) {
            $path = $image->store('products', 'public');
            $product->images()->create(['image_path' => $path]);
        }

        if (! $product->image_path) {
            $product->update(['image_path' => $product->images()->value('image_path')]);
        }
    }
}
