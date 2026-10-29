<?php

namespace Database\Seeders;

use App\Models\Cliente;
use App\Models\Mesero;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoUsuariosSeeder extends Seeder
{
    public function run(): void
    {
        if (User::where('email', 'mesero@casonachapaca.test')->exists()) {
            return;
        }

        $mesero = Mesero::whereNull('user_id')->first();
        $cliente = Cliente::whereNull('user_id')->first();

        if ($mesero) {
            $cuentaMesero = User::factory()->mesero()->create([
                'name' => $mesero->nombreCompleto(),
                'email' => 'mesero@casonachapaca.test',
            ]);
            $mesero->update(['user_id' => $cuentaMesero->id]);
        }

        if ($cliente) {
            $cuentaUsuario = User::factory()->create([
                'name' => $cliente->nombreCompleto(),
                'email' => 'usuario@casonachapaca.test',
            ]);
            $cliente->update(['user_id' => $cuentaUsuario->id]);
        }
    }
}