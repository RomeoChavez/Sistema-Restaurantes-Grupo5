<?php

namespace Database\Seeders;

use App\Models\Cliente;
use App\Models\DetallePedido;
use App\Models\Mesa;
use App\Models\Mesero;
use App\Models\Pedido;
use App\Models\Plato;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PedidoSeeder extends Seeder
{
    private const TOTAL_PEDIDOS = 1000;
    private const PEDIDOS_ACTIVOS = 10;

    public function run(): void
    {
        $clientes = Cliente::pluck('id');
        $mesas = Mesa::pluck('id');
        $meseros = Mesero::pluck('id');
        $platos = Plato::all();

        $mesasOcupadas = $mesas->random(self::PEDIDOS_ACTIVOS)->values();
        $estadosActivos = [
            Pedido::ESTADO_PENDIENTE,
            Pedido::ESTADO_PREPARANDO,
            Pedido::ESTADO_SERVIDO,
        ];

        DB::transaction(function () use ($clientes, $mesas, $meseros, $platos, $mesasOcupadas, $estadosActivos) {
            for ($i = 0; $i < self::TOTAL_PEDIDOS; $i++) {
                $esActivo = $i < self::PEDIDOS_ACTIVOS;

                if ($esActivo) {
                    $estado = $estadosActivos[$i % count($estadosActivos)];
                    $mesaId = $mesasOcupadas[$i];
                    $fechaHora = now()->subMinutes(rand(5, 90));
                } else {
                    $estado = rand(1, 10) === 1 ? Pedido::ESTADO_CANCELADO : Pedido::ESTADO_PAGADO;
                    $mesaId = $mesas->random();
                    $fechaHora = now()->subMinutes(rand(180, 60 * 24 * 90));
                }

                $pedido = Pedido::factory()->create([
                    'cliente_id' => $clientes->random(),
                    'mesa_id' => $mesaId,
                    'mesero_id' => $meseros->random(),
                    'estado' => $estado,
                    'fecha_hora' => $fechaHora,
                ]);

                $this->crearDetalles($pedido, $platos);
            }

            Mesa::whereIn('id', $mesasOcupadas)->update(['disponible' => false]);
        });
    }

    private function crearDetalles(Pedido $pedido, $platos): void
    {
        $detalles = $platos->random(rand(1, 5))->map(fn (Plato $plato) => [
            'pedido_id' => $pedido->id,
            'plato_id' => $plato->id,
            'cantidad' => rand(1, 4),
            'precio_unitario' => $plato->precio,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DetallePedido::insert($detalles->all());
    }
}