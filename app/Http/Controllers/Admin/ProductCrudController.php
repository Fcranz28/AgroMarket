<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProductCrudController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::with('category')->get();
        return view('admin.products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();
        return view('admin.products.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'unit' => 'required|array|min:1',
            'stock' => 'required|array|min:1',
            'price' => 'required|array|min:1',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Procesar imagen
        $imagePath = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            // Guardar en storage/app/public/products
            $imagePath = $file->store('products', 'public');
        }

        // Crear slug
        $slug = Str::slug($request->name) . '-' . Str::random(5);

        // Calculate base values
        $basePrice = min($request->price);
        $baseUnit = $request->unit[0];
        $baseStock = array_sum($request->stock);

        $product = Product::create([
            'category_id' => $validated['category_id'],
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'],
            'price' => $basePrice,
            'unit' => $baseUnit,
            'stock' => $baseStock,
            'image_path' => $imagePath,
        ]);

        // Save product units
        foreach ($request->unit as $key => $unit) {
            if (isset($request->stock[$key]) && isset($request->price[$key])) {
                $product->units()->create([
                    'unit' => $unit,
                    'stock' => $request->stock[$key],
                    'price' => $request->price[$key]
                ]);
            }
        }

        return redirect()->route('admin.productos.index')
                       ->with('success', 'Producto creado correctamente');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = Product::findOrFail($id);
        return view('admin.products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $product = Product::findOrFail($id);
        $categories = Category::all();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'unit' => 'required|array|min:1',
            'stock' => 'required|array|min:1',
            'price' => 'required|array|min:1',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Use database transaction for data integrity
        \DB::transaction(function () use ($request, $validated, $product) {
            $oldImagePath = $product->image_path;
            $newImagePath = null;

            // Process new image if exists
            if ($request->hasFile('image')) {
                $newImagePath = $request->file('image')->store('products', 'public');
                $product->image_path = $newImagePath;
            }

            // Recalculate base values from new units
            $basePrice = min($request->price);
            $baseUnit = $request->unit[0];
            $baseStock = array_sum($request->stock);

            $product->update([
                'category_id' => $validated['category_id'],
                'name' => $validated['name'],
                'description' => $validated['description'],
                'price' => $basePrice,
                'unit' => $baseUnit,
                'stock' => $baseStock,
                'image_path' => $product->image_path,
            ]);

            // Sync product units: delete old units and create new ones
            $product->units()->delete();
            
            foreach ($request->unit as $key => $unit) {
                if (isset($request->stock[$key]) && isset($request->price[$key])) {
                    $product->units()->create([
                        'unit' => $unit,
                        'stock' => $request->stock[$key],
                        'price' => $request->price[$key]
                    ]);
                }
            }

            // Only delete old image if transaction was successful and we have a new image
            if ($newImagePath && $oldImagePath) {
                Storage::disk('public')->delete($oldImagePath);
            }
        });

        return redirect()->route('admin.productos.index')
                       ->with('success', 'Producto actualizado correctamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = Product::findOrFail($id);
        
        // Eliminar imagen
        if ($product->image_path) {
            Storage::disk('public')->delete($product->image_path);
        }
        
        $product->delete();

        return redirect()->route('admin.productos.index')
                       ->with('success', 'Producto eliminado correctamente');
    }
}
