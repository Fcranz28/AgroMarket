<?php

namespace Database\Factories;

use App\Modules\Orders\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Invoice>
 */
class InvoiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $correlativo = str_pad((string) fake()->unique()->numberBetween(1, 999999), 6, '0', STR_PAD_LEFT);
        $total = fake()->randomFloat(2, 10, 500);

        return [
            'order_id' => Order::factory(),
            'tipo_doc' => '03',
            'serie' => 'B001',
            'correlativo' => $correlativo,
            'numero_completo' => "B001-{$correlativo}",
            'subtotal' => round($total / 1.18, 2),
            'igv' => round($total - $total / 1.18, 2),
            'total' => $total,
            'status' => 'success',
        ];
    }
}
