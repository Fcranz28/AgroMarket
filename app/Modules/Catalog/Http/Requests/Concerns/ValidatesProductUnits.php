<?php

namespace App\Modules\Catalog\Http\Requests\Concerns;

use Illuminate\Validation\Validator;

trait ValidatesProductUnits
{
    /**
     * Rules shared by every product form: one stock and one price per selected unit.
     *
     * @return array<string, string>
     */
    protected function productRules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:2000',
            'category_id' => 'required|exists:categories,id',
            'unit' => 'required|array|min:1|max:10',
            'unit.*' => 'required|string|max:50|distinct',
            'stock' => 'required|array',
            'stock.*' => 'required|integer|min:0|max:1000000',
            'price' => 'required|array',
            'price.*' => 'required|numeric|min:0.1|max:999999',
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function productMessages(): array
    {
        return [
            'name.required' => 'Ingresa el nombre del producto.',
            'description.required' => 'Ingresa una descripción.',
            'category_id.required' => 'Selecciona una categoría.',
            'category_id.exists' => 'La categoría seleccionada no existe.',
            'unit.required' => 'Selecciona al menos una unidad de venta.',
            'unit.*.distinct' => 'No repitas la misma unidad.',
            'stock.*.required' => 'Ingresa el stock de cada unidad.',
            'stock.*.integer' => 'El stock debe ser un número entero.',
            'stock.*.min' => 'El stock no puede ser negativo.',
            'price.*.required' => 'Ingresa el precio de cada unidad.',
            'price.*.numeric' => 'El precio debe ser un número.',
            'price.*.min' => 'El precio mínimo es S/. 0.10.',
        ];
    }

    /**
     * @return array<int, \Closure(Validator): void>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $units = count((array) $this->input('unit'));

                if (count((array) $this->input('stock')) !== $units || count((array) $this->input('price')) !== $units) {
                    $validator->errors()->add('unit', 'Cada unidad debe tener su stock y su precio.');
                }
            },
        ];
    }

    /**
     * Units as [unit, stock, price] rows, aligned by position.
     *
     * @return list<array{unit: string, stock: int, price: string}>
     */
    public function units(): array
    {
        return collect($this->validated('unit'))
            ->values()
            ->map(fn (string $unit, int $index) => [
                'unit' => $unit,
                'stock' => (int) array_values($this->validated('stock'))[$index],
                'price' => (string) array_values($this->validated('price'))[$index],
            ])
            ->all();
    }
}
