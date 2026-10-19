<?php

namespace Database\Factories;

use App\Models\Cliente;
use App\Models\Mesa;
use App\Models\Mesero;
use App\Models\Pedido;
use Illuminate\Database\Eloquent\Factories\Factory;

class PedidoFactory extends Factory
{
    protected $model = Pedido::class;

    public function definition(): array
    {
        return [
            'cliente_id' => Cliente::factory(),
            'mesa_id' => Mesa::factory(),
            'mesero_id' => Mesero::factory(),
            'estado' => fake()->randomElement(Pedido::ESTADOS),
            'fecha_hora' => fake()->dateTimeBetween('-30 days', 'now'),
        ];
    }
}