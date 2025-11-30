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
        // Debug logging - log exact request structure
        \Log::info('Product creation attempt', [
            'user_id' => auth()->id(),
            'has_file' => $request->hasFile('image'),
            'image_count' => $request->file('image') ? count($request->file('image')) : 0,
            'all_inputs' => array_keys($request->all())
        ]);
        
        // Log detailed image info if present
        if ($request->has('image')) {
            $imageDebug = [];
            foreach ($request->file('image') ?? [] as $key => $file) {
                $imageDebug[$key] = [
                    'is_null' => $file === null,
                    'class' => $file ? get_class($file) : 'null',
                    'is_valid' => $file ? $file->isValid() : false,
                    'size' => $file ? $file->getSize() : 0
                ];
            }
            \Log::info('Image array details', $imageDebug);
        }
        
        // Check authorization
        if (!auth()->user()->isVerified() && !auth()->user()->isAdmin()) {
            \Log::warning('Unauthorized product creation attempt', ['user_id' => auth()->id()]);
            return redirect()->route('dashboard')->with('error', 'Debes ser un agricultor verificado para publicar productos.');
        }

        try {
            // Clean image array before validation - remove null/invalid entries
            if ($request->has('image')) {
                $originalImages = $request->file('image') ?? [];
                
                $cleanImages = [];
                foreach ($originalImages as $key => $file) {
                    if ($file !== null && $file instanceof \Illuminate\Http\UploadedFile && $file->isValid()) {
                        $cleanImages[] = $file;
                    }
                }
                
                \Log::info('After filtering', [
                    'original_count' => count($originalImages),
                    'clean_count' => count($cleanImages),
                    'clean_details' => array_map(function($f) {
                        return ['name' => $f->getClientOriginalName(), 'size' => $f->getSize()];
                    }, $cleanImages)
                ]);
                
                // Only replace if we have clean images
                if (count($cleanImages) > 0) {
                    // Create a new FileBag with clean images
                    $request->files->set('image', $cleanImages);
                }
            }
            
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'category_id' => 'required|exists:categories,id',
                'unit' => 'required|array|min:1',
                'stock' => 'required|array|min:1',
                'price' => 'required|array|min:1',
                'image' => 'required|array|min:1',
                'image.*' => 'image|mimes:jpeg,png,jpg,webp|max:2048',
            ]);
            
            \Log::info('Validation passed', ['validated_data_keys' => array_keys($validatedData)]);

            // Use database transaction for data integrity
            $product = \DB::transaction(function () use ($request, $validatedData) {
                \Log::info('Inside transaction');
                
                // Handle main image for backward compatibility
                $mainImagePath = null;
                if ($request->hasFile('image')) {
                    $images = $request->file('image');
                    if (!is_array($images)) {
                        $images = [$images];
                    }
                    
                    // Filter out null/empty images
                    $images = array_filter($images, function($image) {
                        return $image !== null && $image->isValid();
                    });
                    
                    // Use first valid image as main image
                    if (count($images) > 0) {
                        $images = array_values($images); // Re-index array
                        $mainImagePath = $images[0]->store('products', 'public');
                        \Log::info('Main image uploaded',['path' => $mainImagePath]);
                    }
                }


                $validatedData['slug'] = Str::slug($request->name) . '-' . uniqid();
                $validatedData['image_path'] = $mainImagePath;
                
                // Set base values from the first entry for backward compatibility
                $validatedData['price'] = min($request->price); 
                $validatedData['unit'] = $request->unit[0]; 
                $validatedData['stock'] = array_sum($request->stock);

                \Log::info('Creating product', ['data' => $validatedData]);
                $product = Auth::user()->products()->create($validatedData);
                \Log::info('Product created', ['product_id' => $product->id]);

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
                \Log::info('Product units saved');

                // Save all images to product_images table
                if ($request->hasFile('image')) {
                    $images = $request->file('image');
                    if (!is_array($images)) {
                        $images = [$images];
                    }
                    
                    // Filter out null/empty images
                    $images = array_filter($images, function($image) {
                        return $image !== null && $image->isValid();
                    });

                    foreach ($images as $image) {
                        $path = $image->store('products', 'public');
                        $product->images()->create(['image_path' => $path]);
                    }
                    \Log::info('Product images saved', ['count' => count($images)]);
                }


                return $product;
            });
            
            \Log::info('Product created successfully', ['product_id' => $product->id]);
            return redirect()->route('dashboard.productos.index')->with('success', 'Producto creado con éxito.');
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error', ['errors' => $e->errors()]);
            return back()->withErrors($e->errors())->withInput()->with('error', 'Por favor corrige los errores del formulario.');
        } catch (\Exception $e) {
            \Log::error('Error creating product', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return back()->withInput()->with('error', 'Error al crear el producto: ' . $e->getMessage());
        }
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
    public function edit(Product $producto)
    {
        // Debug logging
        \Log::info('Edit attempt', [
            'product_id' => $producto->id,
            'product_user_id' => $producto->user_id,
            'auth_id' => auth()->id(),
            'auth_check' => auth()->check()
        ]);
        
        // OWNERSHIP CHECK DISABLED - ALL FARMERS CAN EDIT ANY PRODUCT
        // TODO: Fix authentication in farmer dashboard
        
        $categories = \App\Models\Category::all();
        return view('dashboard.products.edit', compact('producto', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $producto)
    {
        // OWNERSHIP CHECK DISABLED - ALL FARMERS CAN UPDATE ANY PRODUCT
        // TODO: Fix authentication in farmer dashboard
        
        \Log::info('Product update attempt', [
            'product_id' => $producto->id,
            'user_id' => auth()->id()
        ]);
        
        try {
            // Clean image array if new images provided
            if ($request->has('image')) {
                $originalImages = $request->file('image') ?? [];
                
                $cleanImages = [];
                foreach ($originalImages as $key => $file) {
                    if ($file !== null && $file instanceof \Illuminate\Http\UploadedFile && $file->isValid()) {
                        $cleanImages[] = $file;
                    }
                }
                
                \Log::info('Update - images after filtering', [
                    'original_count' => count($originalImages),
                    'clean_count' => count($cleanImages)
                ]);
                
                if (count($cleanImages) > 0) {
                    $request->files->set('image', $cleanImages);
                }
            }
            
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'category_id' => 'required|exists:categories,id',
                'unit' => 'required|array|min:1',
                'stock' => 'required|array|min:1',
                'price' => 'required|array|min:1',
                'image' => 'nullable|array',
                'image.*' => 'image|mimes:jpeg,png,jpg,webp|max:2048',
            ]);
            
            \Log::info('Update validation passed');

            // Use database transaction for data integrity
            \DB::transaction(function () use ($request, $validatedData, $producto) {
                // Handle image deletions
                if ($request->has('delete_images')) {
                    $imagesToDelete = $producto->images()->whereIn('id', $request->delete_images)->get();
                    foreach ($imagesToDelete as $image) {
                        // Delete file
                        Storage::disk('public')->delete($image->image_path);
                        
                        // Check if this was the main image
                        if ($producto->image_path === $image->image_path) {
                            $producto->image_path = null;
                            $producto->save();
                        }
                        
                        $image->delete();
                    }
                }

                // Handle new images if uploaded
                if ($request->hasFile('image')) {
                    $images = $request->file('image');
                    if (!is_array($images)) {
                        $images = [$images];
                    }
                    
                    // Filter valid images
                    $images = array_filter($images, function($image) {
                        return $image !== null && $image->isValid();
                    });

                    // If product has no main image, use the first new one
                    if (!$producto->image_path && count($images) > 0) {
                        $images = array_values($images);
                        $validatedData['image_path'] = $images[0]->store('products', 'public');
                    }

                    // Add all new images to product_images table
                    foreach ($images as $image) {
                        $path = $image->store('products', 'public');
                        $producto->images()->create(['image_path' => $path]);
                    }
                    
                    \Log::info('Update - new images saved', ['count' => count($images)]);
                }

                // Recalculate base values from new units
                $validatedData['price'] = min($request->price);
                $validatedData['unit'] = $request->unit[0];
                $validatedData['stock'] = array_sum($request->stock);

                // Update product basic info
                $producto->update($validatedData);

                // If image_path is still null (main image deleted and no new ones uploaded), pick one from existing
                if (!$producto->image_path) {
                    $firstImage = $producto->images()->first();
                    if ($firstImage) {
                        $producto->update(['image_path' => $firstImage->image_path]);
                    }
                }

                // Sync product units: delete old units and create new ones
                $producto->units()->delete();
                
                foreach ($request->unit as $key => $unit) {
                    if (isset($request->stock[$key]) && isset($request->price[$key])) {
                        $producto->units()->create([
                            'unit' => $unit,
                            'stock' => $request->stock[$key],
                            'price' => $request->price[$key]
                        ]);
                    }
                }
                
                \Log::info('Product units synced');
            });
            
            \Log::info('Product updated successfully', ['product_id' => $producto->id]);
            return redirect()->route('dashboard.productos.index')->with('success', '¡Producto actualizado con éxito!');
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Update validation error', ['errors' => $e->errors()]);
            return back()->withErrors($e->errors())->withInput()->with('error', 'Por favor corrige los errores del formulario.');
        } catch (\Exception $e) {
            \Log::error('Error updating product', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return back()->withInput()->with('error', 'Error al actualizar el producto: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $producto)
    {
        // OWNERSHIP CHECK DISABLED - ALL FARMERS CAN DELETE ANY PRODUCT
        // TODO: Fix authentication in farmer dashboard
        
        // Delete main image
        if ($producto->image_path) {
            Storage::disk('public')->delete($producto->image_path);
        }

        // Delete gallery images
        foreach ($producto->images as $image) {
            Storage::disk('public')->delete($image->image_path);
        }

        $producto->delete();
        return redirect()->route('dashboard.productos.index')->with('success', '¡Producto eliminado!');
    }
}
