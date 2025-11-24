<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;
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
            'Verduras y Hortalizas',
            'Tubérculos y Raíces',
            'Granos y Legumbres',
            'Hierbas y Aromáticas',
            'Semillas y Plantones',
            'Insumos Agrícolas',
            'Herramientas Manuales',
            'Maquinaria Agrícola',
            'Sistemas de Riego',
            'Tecnología Agrícola',
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['name' => $category],
                ['slug' => Str::slug($category)]
            );
        }
    }
}
