<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mesero extends Model
{
    use HasFactory;

    protected $fillable = ['nombre', 'apellido', 'telefono'];

    public function pedidos(): HasMany
    {
        return $this->hasMany(Pedido::class);
    }

    public function nombreCompleto(): string
    {
        return $this->nombre . ' ' . $this->apellido;
    }
}