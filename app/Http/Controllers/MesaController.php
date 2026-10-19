<?php

namespace App\Http\Controllers;

use App\Models\Mesa;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MesaController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $consulta = Mesa::query()->orderBy('numero');

        if ($request->boolean('solo_disponibles')) {
            $consulta->where('disponible', true);
        }

        return response()->json($consulta->get());
    }

    public function store(Request $request): JsonResponse
    {
        $datos = $request->validate([
            'numero' => ['required', 'integer', 'min:1', 'unique:mesas,numero'],
            'capacidad' => ['required', 'integer', 'min:1', 'max:20'],
        ]);

        $mesa = Mesa::create($datos);

        return response()->json($mesa, 201);
    }

    public function show(Mesa $mesa): JsonResponse
    {
        return response()->json($mesa);
    }

    public function update(Request $request, Mesa $mesa): JsonResponse
    {
        $datos = $request->validate([
            'numero' => ['sometimes', 'integer', 'min:1', 'unique:mesas,numero,' . $mesa->id],
            'capacidad' => ['sometimes', 'integer', 'min:1', 'max:20'],
            'disponible' => ['sometimes', 'boolean'],
        ]);

        $mesa->update($datos);

        return response()->json($mesa);
    }

    public function destroy(Mesa $mesa): JsonResponse
    {
        if ($mesa->pedidos()->exists()) {
            return response()->json(['mensaje' => 'La mesa tiene pedidos registrados'], 422);
        }

        $mesa->delete();

        return response()->json(null, 204);
    }
}