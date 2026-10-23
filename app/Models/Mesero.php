<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mesero extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'nombre', 'apellido', 'telefono'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function pedidos(): HasMany
    {
        return $this->hasMany(Pedido::class);
    }

    public function nombreCompleto(): string
    {
        return $this->nombre . ' ' . $this->apellido;
    }
}