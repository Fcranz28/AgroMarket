<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Frutas',
            'Verduras',
            'Tubérculos',
            'Granos y Cereales',
            'Legumbres',
            'Hierbas y Especias',
            'Lácteos y Derivados',
            'Carnes y Aves',
            'Huevos',
            'Miel y Derivados',
            'Frutos Secos',
            'Semillas',
            'Flores y Plantas',
            'Procesados Artesanales'
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['name' => $category],
                ['slug' => Str::slug($category)]
            );
        }

        $this->command->info('Categorías insertadas correctamente.');
    }
}
