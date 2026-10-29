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
        ], [
            'nombre.unique' => 'Este plato ya está registrado en la categoría seleccionada.',
            'categoria_id.required' => 'Debes seleccionar una categoría.',
            'categoria_id.exists' => 'La categoría seleccionada no existe.',
            'nombre.required' => 'El nombre del plato es obligatorio.',
            'precio.required' => 'El precio del plato es obligatorio.',
            'precio.numeric' => 'El precio debe ser un número válido.',
            'precio.min' => 'El precio debe ser de al menos Bs. 0.50.',
        ]);

        $datos['disponible'] = $request->boolean('disponible', true);

        Plato::create($datos);

        return redirect()->route('admin.inicio', ['tab' => 'platos'])
            ->with('success', 'El plato "' . $datos['nombre'] . '" fue registrado exitosamente en la carta.')
            ->with('pestana', 'platos');
    }

    public function toggle(Plato $plato): RedirectResponse
    {
        $plato->update(['disponible' => !$plato->disponible]);

        $mensaje = $plato->disponible
            ? 'El plato ' . $plato->nombre . ' ahora está disponible.'
            : 'El plato ' . $plato->nombre . ' fue marcado como agotado.';

        return redirect()->route('admin.inicio', ['tab' => 'platos'])
            ->with('success', $mensaje)
            ->with('pestana', 'platos');
    }
}
