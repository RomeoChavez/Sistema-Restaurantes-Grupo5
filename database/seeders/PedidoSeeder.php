<?php

namespace Database\Seeders;

use App\Models\Cliente;
use App\Models\DetallePedido;
use App\Models\Mesa;
use App\Models\Mesero;
use App\Models\Pedido;
use App\Models\Plato;
use Illuminate\Database\Seeder;

class PedidoSeeder extends Seeder
{
    public function run(): void
    {
        $clientes = Cliente::all();
        $mesas = Mesa::all();
        $meseros = Mesero::all();
        $platos = Plato::all();

        for ($i = 0; $i < 60; $i++) {
            $pedido = Pedido::factory()->create([
                'cliente_id' => $clientes->random()->id,
                'mesa_id' => $mesas->random()->id,
                'mesero_id' => $meseros->random()->id,
            ]);

            $platosElegidos = $platos->random(rand(1, 4));

            foreach ($platosElegidos as $plato) {
                DetallePedido::factory()->create([
                    'pedido_id' => $pedido->id,
                    'plato_id' => $plato->id,
                    'precio_unitario' => $plato->precio,
                ]);
            }
        }
    }
}