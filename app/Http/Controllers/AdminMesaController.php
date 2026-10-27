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
        ], [
            'numero.unique' => 'Ya existe una mesa registrada con el número ingresado.',
            'numero.required' => 'El número de mesa es obligatorio.',
            'numero.integer' => 'El número de mesa debe ser un entero.',
            'numero.min' => 'El número de mesa debe ser al menos 1.',
            'capacidad.required' => 'La capacidad de personas es obligatoria.',
            'capacidad.min' => 'La capacidad mínima debe ser de al menos 1 persona.',
            'capacidad.max' => 'La capacidad máxima permitida es de 20 personas.',
        ]);

        Mesa::create([
            'numero' => $datos['numero'],
            'capacidad' => $datos['capacidad'],
            'estado' => Mesa::ESTADO_LIBRE,
            'disponible' => true,
        ]);

        return redirect()->route('admin.inicio', ['tab' => 'mesas'])
            ->with('success', 'Mesa #' . $datos['numero'] . ' creada correctamente.')
            ->with('pestana', 'mesas');
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

        return redirect()->route('admin.inicio', ['tab' => 'mesas'])
            ->with('success', 'Estado de la Mesa #' . $mesa->numero . ' actualizado a ' . $datos['estado'] . '.')
            ->with('pestana', 'mesas');
    }
}
