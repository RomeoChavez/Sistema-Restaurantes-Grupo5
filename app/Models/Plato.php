<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plato extends Model
{
    use HasFactory;

    public const IMAGEN_POR_DEFECTO = 'img/plato-default.svg';

    protected $fillable = ['categoria_id', 'nombre', 'descripcion', 'imagen', 'precio', 'disponible'];

    protected $casts = [
        'precio' => 'decimal:2',
        'disponible' => 'boolean',
    ];

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class);
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(DetallePedido::class);
    }

    public function pedidos(): BelongsToMany
    {
        return $this->belongsToMany(Pedido::class, 'detalle_pedidos', 'plato_id', 'pedido_id')
            ->withPivot('cantidad', 'precio_unitario')
            ->withTimestamps();
    }

    public function scopeDisponibles(Builder $consulta): Builder
    {
        return $consulta->where('disponible', true);
    }

    protected function imagenUrl(): Attribute
    {
        return Attribute::get(fn () => $this->imagen
            ? asset('storage/' . $this->imagen)
            : asset(self::IMAGEN_POR_DEFECTO));
    }
}