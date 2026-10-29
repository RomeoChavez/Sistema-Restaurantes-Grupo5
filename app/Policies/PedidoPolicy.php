<?php

namespace App\Policies;

use App\Models\Pedido;
use App\Models\User;

class PedidoPolicy
{
    public function ver(User $usuario, Pedido $pedido): bool
    {
        return $this->esPersonal($usuario) || $this->esDuenio($usuario, $pedido);
    }

    public function crear(User $usuario): bool
    {
        return true;
    }

    public function agregarPlatos(User $usuario, Pedido $pedido): bool
    {
        if ($this->esPersonal($usuario)) {
            return ! $pedido->estaCerrado();
        }

        return $this->esDuenio($usuario, $pedido) && $pedido->esPendiente();
    }

    public function cancelar(User $usuario, Pedido $pedido): bool
    {
        if ($this->esPersonal($usuario)) {
            return $pedido->puedePasarA(Pedido::ESTADO_CANCELADO);
        }

        return $this->esDuenio($usuario, $pedido) && $pedido->esPendiente();
    }

    public function cambiarEstado(User $usuario, Pedido $pedido): bool
    {
        return $this->esPersonal($usuario) && ! $pedido->estaCerrado();
    }

    public function tomar(User $usuario, Pedido $pedido): bool
    {
        return $this->esPersonal($usuario) && $pedido->mesero_id === null && ! $pedido->estaCerrado();
    }

    private function esPersonal(User $usuario): bool
    {
        return $usuario->tieneRol(User::ROL_ADMIN, User::ROL_MESERO);
    }

    private function esDuenio(User $usuario, Pedido $pedido): bool
    {
        return (int) $pedido->cliente?->user_id === $usuario->id;
    }
}