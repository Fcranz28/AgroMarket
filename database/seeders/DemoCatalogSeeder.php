<?php

namespace Database\Seeders;

use App\Modules\Catalog\Models\Category;
use App\Modules\Catalog\Models\Product;
use App\Modules\Identity\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Demo catalog for local development: a verified farmer with products in several units.
 * Only runs in the local environment (see DatabaseSeeder).
 */
class DemoCatalogSeeder extends Seeder
{
    /**
     * @var array<string, list<array{name: string, description: string, units: list<array{unit: string, price: string, stock: int}>}>>
     */
    private const PRODUCTS = [
        'Tubérculos' => [
            ['name' => 'Papa amarilla', 'description' => 'Papa amarilla de Huánuco, ideal para causa y puré.', 'units' => [
                ['unit' => 'kg', 'price' => '4.50', 'stock' => 120],
                ['unit' => 'saco (50 kg)', 'price' => '190.00', 'stock' => 8],
            ]],
            ['name' => 'Camote morado', 'description' => 'Camote dulce de Cañete.', 'units' => [
                ['unit' => 'kg', 'price' => '3.20', 'stock' => 80],
            ]],
        ],
        'Frutas' => [
            ['name' => 'Palta Hass', 'description' => 'Palta cremosa de La Libertad.', 'units' => [
                ['unit' => 'kg', 'price' => '9.90', 'stock' => 60],
                ['unit' => 'caja (10 kg)', 'price' => '89.00', 'stock' => 12],
            ]],
            ['name' => 'Mango Kent', 'description' => 'Mango de exportación de Piura.', 'units' => [
                ['unit' => 'kg', 'price' => '6.50', 'stock' => 90],
            ]],
            ['name' => 'Aguaymanto', 'description' => 'Aguaymanto orgánico de Cajamarca.', 'units' => [
                ['unit' => '250 g', 'price' => '5.00', 'stock' => 40],
            ]],
        ],
        'Granos y Cereales' => [
            ['name' => 'Quinua blanca', 'description' => 'Quinua orgánica de Puno.', 'units' => [
                ['unit' => 'kg', 'price' => '12.00', 'stock' => 70],
                ['unit' => 'arroba', 'price' => '140.00', 'stock' => 6],
            ]],
            ['name' => 'Maíz morado', 'description' => 'Para chicha y mazamorra.', 'units' => [
                ['unit' => 'kg', 'price' => '7.80', 'stock' => 50],
            ]],
        ],
        'Verduras' => [
            ['name' => 'Ají amarillo', 'description' => 'Fresco, de Ica.', 'units' => [
                ['unit' => 'kg', 'price' => '8.00', 'stock' => 35],
            ]],
            ['name' => 'Tomate italiano', 'description' => 'Cosecha de la semana.', 'units' => [
                ['unit' => 'kg', 'price' => '3.90', 'stock' => 100],
            ]],
        ],
        'Miel y Derivados' => [
            ['name' => 'Miel de abeja', 'description' => 'Miel pura de Oxapampa.', 'units' => [
                ['unit' => 'frasco 500 g', 'price' => '22.00', 'stock' => 25],
            ]],
        ],
    ];

    public function run(): void
    {
        $farmer = User::firstOrNew(['email' => 'agricultor@agromarket.test']);
        $farmer->forceFill([
            'name' => 'Rosa Mamani',
            'password' => 'password',
            'role' => 'farmer',
            'verification_status' => 'approved',
            'onboarding_completed' => true,
            'email_verified_at' => now(),
        ])->save();

        foreach (self::PRODUCTS as $categoryName => $products) {
            $category = Category::firstOrCreate(['name' => $categoryName], ['slug' => Str::slug($categoryName)]);

            foreach ($products as $data) {
                if (Product::where('name', $data['name'])->exists()) {
                    continue;
                }

                $product = Product::create([
                    'user_id' => $farmer->id,
                    'category_id' => $category->id,
                    'name' => $data['name'],
                    'slug' => Str::slug($data['name']).'-'.Str::random(6),
                    'description' => $data['description'],
                    'unit' => $data['units'][0]['unit'],
                    'price' => 0,
                    'stock' => 0,
                ]);

                $product->syncUnits($data['units']);
            }
        }

        $this->command->info('Catálogo de demo listo (agricultor: agricultor@agromarket.test / password).');
    }
}
