<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Cliente;
use App\Models\Mesa;
use App\Models\Pedido;
use App\Models\Plato;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ClientePedidoController extends Controller
{
    public function create(): View
    {
        $mesas = Mesa::libres()->orderBy('numero')->get();
        $categorias = Categoria::with(['platos' => fn ($consulta) => $consulta->disponibles()->orderBy('nombre')])
            ->whereHas('platos', fn ($consulta) => $consulta->disponibles())
            ->get();

        return view('cliente.reservar', compact('mesas', 'categorias'));
    }

    public function store(Request $request): RedirectResponse
    {
        $datos = $request->validate([
            'mesa_id' => ['required', 'exists:mesas,id'],
            'platos' => ['required', 'array', 'min:1'],
            'platos.*.id' => ['required', 'exists:platos,id'],
            'platos.*.cantidad' => ['required', 'integer', 'min:0', 'max:50'],
        ]);

        $mesa = Mesa::findOrFail($datos['mesa_id']);

        if (!$mesa->esLibre()) {
            throw ValidationException::withMessages([
                'mesa_id' => 'La mesa seleccionada ya no se encuentra libre.',
            ]);
        }

        $itemsSeleccionados = array_filter($datos['platos'], fn ($item) => ((int) ($item['cantidad'] ?? 0)) > 0);

        if (empty($itemsSeleccionados)) {
            throw ValidationException::withMessages([
                'platos' => 'Debes seleccionar al menos un plato con cantidad mayor a cero.',
            ]);
        }

        $usuario = $request->user();
        $cliente = $usuario->cliente;

        if (!$cliente) {
            $cliente = Cliente::create([
                'user_id' => $usuario->id,
                'nombre' => $usuario->name,
                'apellido' => '',
            ]);
        }

        DB::transaction(function () use ($cliente, $mesa, $itemsSeleccionados) {
            $pedido = Pedido::create([
                'cliente_id' => $cliente->id,
                'mesa_id' => $mesa->id,
                'mesero_id' => null,
                'estado' => Pedido::ESTADO_PENDIENTE,
                'fecha_hora' => now(),
            ]);

            foreach ($itemsSeleccionados as $item) {
                $plato = Plato::findOrFail($item['id']);

                if (!$plato->disponible) {
                    abort(422, 'El plato ' . $plato->nombre . ' no se encuentra disponible.');
                }

                $pedido->detalles()->create([
                    'plato_id' => $plato->id,
                    'cantidad' => (int) $item['cantidad'],
                    'precio_unitario' => $plato->precio,
                ]);
            }

            $mesa->marcarReservada();
        });

        return redirect()->route('cliente.pedidos')->with('success', '¡Tu reserva y pedido fueron registrados exitosamente! Mesa #' . $mesa->numero . ' reservada.');
    }

    public function index(Request $request): View
    {
        $cliente = $request->user()->cliente;

        $pedidos = $cliente
            ? $cliente->pedidos()->with(['mesa', 'detalles.plato'])->orderByDesc('fecha_hora')->paginate(8)
            : Pedido::whereNull('id')->paginate(8);

        return view('cliente.mis-pedidos', compact('pedidos'));
    }
}
