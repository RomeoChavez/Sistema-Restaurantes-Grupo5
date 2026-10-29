<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

class CrearAdministrador extends Command
{
    protected $signature = 'app:crear-admin';

    protected $description = 'Crea un usuario con rol de administrador';

    public function handle(): int
    {
        $datos = [
            'name' => $this->ask('Nombre'),
            'email' => $this->ask('Correo electrónico'),
            'password' => $this->secret('Contraseña'),
            'password_confirmation' => $this->secret('Repite la contraseña'),
        ];

        $validador = Validator::make($datos, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'min:8', 'confirmed'],
        ]);

        if ($validador->fails()) {
            foreach ($validador->errors()->all() as $mensaje) {
                $this->error($mensaje);
            }

            return self::FAILURE;
        }

        $administrador = new User([
            'name' => $datos['name'],
            'email' => $datos['email'],
            'password' => $datos['password'],
        ]);
        $administrador->rol = User::ROL_ADMIN;
        $administrador->save();

        $this->info('Administrador creado: ' . $administrador->email);

        return self::SUCCESS;
    }
}