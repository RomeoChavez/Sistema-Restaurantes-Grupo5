<?php

namespace App\Http\Controllers;

use App\Models\Plato;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminPlatoController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $datos = $request->validate([
            'categoria_id' => ['required', 'exists:categorias,id'],
            'nombre' => [
                'required',
                'string',
                'max:100',
                Rule::unique('platos')->where('categoria_id', $request->categoria_id),
            ],
            'precio' => ['required', 'numeric', 'min:0.5', 'max:9999.99'],
            'descripcion' => ['nullable', 'string', 'max:500'],
            'disponible' => ['nullable', 'boolean'],
        ]);

        $datos['disponible'] = $request->boolean('disponible', true);

        Plato::create($datos);

        return redirect()->route('admin.inicio')->with('success', 'El plato fue registrado exitosamente en la carta.');
    }

    public function toggle(Plato $plato): RedirectResponse
    {
        $plato->update(['disponible' => !$plato->disponible]);

        $mensaje = $plato->disponible
            ? 'El plato ' . $plato->nombre . ' ahora está disponible.'
            : 'El plato ' . $plato->nombre . ' fue marcado como agotado.';

        return back()->with('success', $mensaje);
    }
}
