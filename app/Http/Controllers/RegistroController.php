<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegistroRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class RegistroController extends Controller
{
    public function create(): View
    {
        return view('auth.registro');
    }

    public function store(RegistroRequest $request): RedirectResponse
    {
        $datos = $request->validated();

        $usuario = DB::transaction(function () use ($datos) {
            $nuevo = new User([
                'name' => $datos['nombre'] . ' ' . $datos['apellido'],
                'email' => $datos['email'],
                'password' => $datos['password'],
            ]);
            $nuevo->rol = User::ROL_USUARIO;
            $nuevo->save();

            $nuevo->cliente()->create([
                'nombre' => $datos['nombre'],
                'apellido' => $datos['apellido'],
                'telefono' => $datos['telefono'] ?? null,
            ]);

            return $nuevo;
        });

        Auth::login($usuario);
        $request->session()->regenerate();

        return redirect()->route('inicio')->with('success', 'Tu cuenta fue creada correctamente.');
    }
}