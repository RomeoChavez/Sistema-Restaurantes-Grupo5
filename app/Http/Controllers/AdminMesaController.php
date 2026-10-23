<?php

namespace App\Http\Controllers;

use App\Models\Mesa;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminMesaController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $datos = $request->validate([
            'numero' => ['required', 'integer', 'min:1', 'unique:mesas,numero'],
            'capacidad' => ['required', 'integer', 'min:1', 'max:20'],
        ]);

        Mesa::create([
            'numero' => $datos['numero'],
            'capacidad' => $datos['capacidad'],
            'estado' => Mesa::ESTADO_LIBRE,
            'disponible' => true,
        ]);

        return redirect()->route('admin.inicio')->with('success', 'Mesa #' . $datos['numero'] . ' creada correctamente.');
    }

    public function cambiarEstado(Request $request, Mesa $mesa): RedirectResponse
    {
        $datos = $request->validate([
            'estado' => ['required', Rule::in([Mesa::ESTADO_LIBRE, Mesa::ESTADO_RESERVADO, Mesa::ESTADO_OCUPADO])],
        ]);

        $mesa->update([
            'estado' => $datos['estado'],
            'disponible' => $datos['estado'] === Mesa::ESTADO_LIBRE,
        ]);

        return back()->with('success', 'Estado de la Mesa #' . $mesa->numero . ' actualizado a ' . $datos['estado'] . '.');
    }
}
