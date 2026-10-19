<?php

namespace App\Http\Controllers;

use App\Models\Plato;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PlatoController extends Controller
{
    public function index(): JsonResponse
    {
        $platos = Plato::with('categoria')->orderBy('nombre')->get();

        return response()->json($platos);
    }

    public function store(Request $request): JsonResponse
    {
        $datos = $request->validate([
            'categoria_id' => ['required', 'exists:categorias,id'],
            'nombre' => [
                'required',
                'string',
                'max:100',
                Rule::unique('platos')->where('categoria_id', $request->categoria_id),
            ],
            'precio' => ['required', 'numeric', 'min:0'],
            'disponible' => ['boolean'],
        ]);

        $plato = Plato::create($datos);

        return response()->json($plato->load('categoria'), 201);
    }

    public function show(Plato $plato): JsonResponse
    {
        return response()->json($plato->load('categoria'));
    }

    public function update(Request $request, Plato $plato): JsonResponse
    {
        $datos = $request->validate([
            'categoria_id' => ['sometimes', 'exists:categorias,id'],
            'nombre' => ['sometimes', 'string', 'max:100'],
            'precio' => ['sometimes', 'numeric', 'min:0'],
            'disponible' => ['sometimes', 'boolean'],
        ]);

        $plato->update($datos);

        return response()->json($plato->load('categoria'));
    }

    public function destroy(Plato $plato): JsonResponse
    {
        if ($plato->detalles()->exists()) {
            return response()->json(['mensaje' => 'El plato tiene pedidos asociados, marquelo como no disponible'], 422);
        }

        $plato->delete();

        return response()->json(null, 204);
    }
}