<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    public const ROL_ADMIN = 'admin';
    public const ROL_MESERO = 'mesero';
    public const ROL_USUARIO = 'usuario';

    public const ROLES = [
        self::ROL_ADMIN => 'Administrador',
        self::ROL_MESERO => 'Mesero',
        self::ROL_USUARIO => 'Usuario',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function mesero(): HasOne
    {
        return $this->hasOne(Mesero::class);
    }

    public function cliente(): HasOne
    {
        return $this->hasOne(Cliente::class);
    }

    public function tieneRol(string ...$roles): bool
    {
        return in_array($this->rol, $roles, true);
    }

    public function esAdmin(): bool
    {
        return $this->rol === self::ROL_ADMIN;
    }

    public function esMesero(): bool
    {
        return $this->rol === self::ROL_MESERO;
    }

    public function esUsuario(): bool
    {
        return $this->rol === self::ROL_USUARIO;
    }
}