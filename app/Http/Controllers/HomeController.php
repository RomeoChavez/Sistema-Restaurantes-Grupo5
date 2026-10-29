<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Mesa;
use App\Models\Pedido;
use App\Models\Plato;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $categorias = Categoria::with(['platos' => fn ($consulta) => $consulta->disponibles()])
            ->whereHas('platos', fn ($consulta) => $consulta->disponibles())
            ->get();

        $mesasLibres = Mesa::libres()->count();
        $totalMesas = Mesa::count();

        return view('inicio', compact('categorias', 'mesasLibres', 'totalMesas'));
    }

    public function admin(): View
    {
        $categorias = Categoria::withCount('platos')->orderBy('nombre')->get();
        $platos = Plato::with('categoria')->orderBy('categoria_id')->orderBy('nombre')->get();
        $mesas = Mesa::orderBy('numero')->get();

        $conteoMesas = [
            'libres' => Mesa::libres()->count(),
            'reservadas' => Mesa::reservadas()->count(),
            'ocupadas' => Mesa::ocupadas()->count(),
            'total' => $mesas->count(),
        ];

        $totalPedidosActivos = Pedido::activos()->count();

        return view('admin.inicio', compact('categorias', 'platos', 'mesas', 'conteoMesas', 'totalPedidosActivos'));
    }

    public function cocina(): View
    {
        $pendientes = Pedido::with(['cliente', 'mesa', 'detalles.plato'])
            ->where('estado', Pedido::ESTADO_PENDIENTE)
            ->orderBy('fecha_hora')
            ->get();

        $preparando = Pedido::with(['cliente', 'mesa', 'detalles.plato'])
            ->where('estado', Pedido::ESTADO_PREPARANDO)
            ->orderBy('fecha_hora')
            ->get();

        $listos = Pedido::with(['cliente', 'mesa', 'detalles.plato'])
            ->where('estado', Pedido::ESTADO_LISTO)
            ->orderBy('fecha_hora')
            ->get();

        $servidos = Pedido::with(['cliente', 'mesa', 'detalles.plato'])
            ->where('estado', Pedido::ESTADO_SERVIDO)
            ->orderBy('fecha_hora')
            ->get();

        $mesas = Mesa::orderBy('numero')->get();

        return view('cocina.inicio', compact('pendientes', 'preparando', 'listos', 'servidos', 'mesas'));
    }

    public function cambiarEstadoPedido(Request $request, Pedido $pedido): RedirectResponse
    {
        $datos = $request->validate([
            'estado' => ['required', Rule::in(Pedido::ESTADOS)],
        ]);

        $pedido->cambiarEstado($datos['estado']);

        return back()->with('success', 'Pedido #' . $pedido->id . ' actualizado al estado: ' . $datos['estado'] . '.');
    }
}