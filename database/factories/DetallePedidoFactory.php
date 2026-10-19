<?php

namespace Database\Factories;

use App\Models\DetallePedido;
use App\Models\Pedido;
use App\Models\Plato;
use Illuminate\Database\Eloquent\Factories\Factory;

class DetallePedidoFactory extends Factory
{
    protected $model = DetallePedido::class;

    public function definition(): array
    {
        return [
            'pedido_id' => Pedido::factory(),
            'plato_id' => Plato::factory(),
            'cantidad' => fake()->numberBetween(1, 5),
            'precio_unitario' => fn (array $atributos) => Plato::find($atributos['plato_id'])->precio,
        ];
    }
}