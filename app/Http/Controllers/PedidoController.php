<?php

namespace App\Http\Controllers;

use App\Models\Mesa;
use App\Models\Pedido;
use App\Models\Plato;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PedidoController extends Controller
{
    public function index(): JsonResponse
    {
        $pedidos = Pedido::with(['cliente', 'mesa', 'mesero'])
            ->orderByDesc('fecha_hora')
            ->paginate(15);

        return response()->json($pedidos);
    }

    public function store(Request $request): JsonResponse
    {
        $datos = $request->validate([
            'cliente_id' => ['required', 'exists:clientes,id'],
            'mesa_id' => ['required', 'exists:mesas,id'],
            'mesero_id' => ['required', 'exists:meseros,id'],
            'platos' => ['required', 'array', 'min:1'],
            'platos.*.plato_id' => ['required', 'distinct', 'exists:platos,id'],
            'platos.*.cantidad' => ['required', 'integer', 'min:1'],
        ]);

        $mesa = Mesa::findOrFail($datos['mesa_id']);

        if (!$mesa->disponible) {
            return response()->json(['mensaje' => 'La mesa seleccionada no esta disponible'], 422);
        }

        $pedido = DB::transaction(function () use ($datos, $mesa) {
            $pedido = Pedido::create([
                'cliente_id' => $datos['cliente_id'],
                'mesa_id' => $datos['mesa_id'],
                'mesero_id' => $datos['mesero_id'],
                'estado' => Pedido::ESTADO_PENDIENTE,
            ]);

            foreach ($datos['platos'] as $item) {
                $plato = Plato::findOrFail($item['plato_id']);

                if (!$plato->disponible) {
                    abort(422, 'El plato ' . $plato->nombre . ' no esta disponible');
                }

                $pedido->detalles()->create([
                    'plato_id' => $plato->id,
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $plato->precio,
                ]);
            }

            $mesa->update(['disponible' => false]);

            return $pedido;
        });

        $pedido->load(['cliente', 'mesa', 'mesero', 'detalles.plato']);

        return response()->json([
            'pedido' => $pedido,
            'total' => $pedido->total(),
        ], 201);
    }

    public function show(Pedido $pedido): JsonResponse
    {
        $pedido->load(['cliente', 'mesa', 'mesero', 'detalles.plato']);

        return response()->json([
            'pedido' => $pedido,
            'total' => $pedido->total(),
        ]);
    }

    public function update(Request $request, Pedido $pedido): JsonResponse
    {
        $datos = $request->validate([
            'estado' => ['required', Rule::in(Pedido::ESTADOS)],
        ]);

        if ($pedido->estaCerrado()) {
            return response()->json(['mensaje' => 'El pedido ya esta cerrado'], 422);
        }

        DB::transaction(function () use ($pedido, $datos) {
            $pedido->update(['estado' => $datos['estado']]);

            if ($pedido->estaCerrado()) {
                $pedido->mesa->update(['disponible' => true]);
            }
        });

        return response()->json($pedido->fresh());
    }

    public function destroy(Pedido $pedido): JsonResponse
    {
        if ($pedido->estado !== Pedido::ESTADO_PENDIENTE) {
            return response()->json(['mensaje' => 'Solo se pueden eliminar pedidos pendientes'], 422);
        }

        DB::transaction(function () use ($pedido) {
            $pedido->mesa->update(['disponible' => true]);
            $pedido->delete();
        });

        return response()->json(null, 204);
    }
}